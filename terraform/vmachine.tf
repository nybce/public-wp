resource "azurerm_lb" "vmss" {
  name                = "lb-${var.project}-${var.environment}"
  location            = azurerm_resource_group.vmss.location
  resource_group_name = azurerm_resource_group.vmss.name

  frontend_ip_configuration {
    name                 = "PublicIPAddress"
    public_ip_address_id = azurerm_public_ip.vmss.id
  }
}

resource "azurerm_lb_backend_address_pool" "bpepool" {
  resource_group_name = azurerm_resource_group.vmss.name
  loadbalancer_id     = azurerm_lb.vmss.id
  name                = "bpepool-${var.project}-${var.environment}"
}

resource "azurerm_lb_nat_pool" "lbnatpool" {
  resource_group_name            = azurerm_resource_group.vmss.name
  name                           = "ssh"
  loadbalancer_id                = azurerm_lb.vmss.id
  protocol                       = "Tcp"
  frontend_port_start            = 50000
  frontend_port_end              = 50119
  backend_port                   = 22
  frontend_ip_configuration_name = "PublicIPAddress"
}

resource "azurerm_lb_probe" "vmss" {
  resource_group_name = azurerm_resource_group.vmss.name
  loadbalancer_id     = azurerm_lb.vmss.id
  name                = "http-probe"
  protocol            = "Http"
  request_path        = "/static/health.html"
  port                = var.application_port
}

resource "azurerm_lb_rule" "http" {
  resource_group_name            = azurerm_resource_group.vmss.name
  loadbalancer_id                = azurerm_lb.vmss.id
  name                           = "LBRule-http-${var.project}-${var.environment}"
  protocol                       = "Tcp"
  frontend_port                  = 80
  backend_port                   = var.application_port
  backend_address_pool_id        = azurerm_lb_backend_address_pool.bpepool.id
  frontend_ip_configuration_name = "PublicIPAddress"
  probe_id                       = azurerm_lb_probe.vmss.id
}

resource "azurerm_lb_rule" "https" {
  resource_group_name            = azurerm_resource_group.vmss.name
  loadbalancer_id                = azurerm_lb.vmss.id
  name                           = "LBRule-https-${var.project}-${var.environment}"
  protocol                       = "Tcp"
  frontend_port                  = 443
  backend_port                   = 443
  backend_address_pool_id        = azurerm_lb_backend_address_pool.bpepool.id
  frontend_ip_configuration_name = "PublicIPAddress"
  probe_id                       = azurerm_lb_probe.vmss.id
}

data "template_file" "init" {
  template = file("ec2-init.sh.tpl")

  vars = {
    docker_registry_host     = var.docker_registry_host
    docker_registry_username = var.docker_registry_username
    docker_registry_password = var.docker_registry_password
    docker_image_tag         = var.docker_image_tag
    ansible_vault_pass       = var.ansible_vault_pass
    environment              = var.environment
    nginx_auth_basic         = var.nginx_auth_basic
  }
}

resource "azurerm_linux_virtual_machine_scale_set" "vmss" {
  name                = "vmachine-set-${var.project}-${var.environment}"
  location            = azurerm_resource_group.vmss.location
  resource_group_name = azurerm_resource_group.vmss.name
  sku                 = "Standard_B1ms"
  instances           = 1
  admin_username      = "azureuser"
  custom_data         = base64encode(data.template_file.init.rendered)

  admin_ssh_key {
    username   = "azureuser"
    public_key = "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQDFNL+Jk6r/5+dsgblpSgH3c0Ay8Ii5L3FZhJYn76DNH9c/8EZGDCryWia0dWQPg8NIM0jhEX5Z8Y5NNFn5Z2lTxsUSwv/c2WLLEXeKVbKlsZH8iyET13P701qTRdaSVB8GrgNCD8T8kPam28ZCTXt30dd9fPO3IJW58xiKu5FtnxJs30iv+Ww9VZ54s6Y6HjN78yrQQ5YwDCfRj9t8gZ4fOWKBcUJRWapHZ/gMNLM491PmOJwxzggNEwzgl3UfAelO6fQahInQvlHr0Q3HojFlbmi6FjeRGW+cgrQcaeN4At4w0MszLVLa6pzU2CAUzRJK67jT+6APSaOGxPomQla5 blenderorders@ip-212-30-1-160"
  }

  source_image_reference {
    publisher = "Canonical"
    offer     = "UbuntuServer"
    sku       = "18.04-LTS"
    version   = "latest"
  }

  os_disk {
    storage_account_type = "Standard_LRS"
    caching              = "ReadWrite"
  }

  network_interface {
    name                      = "nic-${var.project}-${var.environment}"
    primary                   = true
    network_security_group_id = azurerm_network_security_group.nsg.id

    ip_configuration {
      name                                   = "internal"
      primary                                = true
      subnet_id                              = azurerm_subnet.vmss.id
      load_balancer_backend_address_pool_ids = [azurerm_lb_backend_address_pool.bpepool.id]
      load_balancer_inbound_nat_rules_ids    = [azurerm_lb_nat_pool.lbnatpool.id]
    }
  }

  tags = {
    Name        = "${var.environment}-cert-${var.project}"
    Environment = var.environment
  }
}
