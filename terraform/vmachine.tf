locals {
  backend_address_pool_name      = "${var.environment}-${var.project}-be-ap"
  frontend_http_port_name        = "${var.environment}-${var.project}-fe-http-pconf"
  frontend_https_port_name       = "${var.environment}-${var.project}-fe-https-pconf"
  frontend_ip_configuration_name = "${var.environment}-${var.project}-fe-ipconf"
  http_setting_name              = "${var.environment}-${var.project}-be-htst"
  listener_http_name             = "${var.environment}-${var.project}-http-lstn"
  listener_https_name            = "${var.environment}-${var.project}-https-lstn"
  request_routing_rule_name      = "${var.environment}-${var.project}-rqrt"
  redirect_configuration_name    = "${var.environment}-${var.project}-rdrcfg"
  ssl_profile_name               = "${var.environment}-${var.project}-sslprof"
  domain_name_sets = {
    "dev" = [
      "comprehensivecellsolutions.dev.bbox.ly",
      "delmarvablood.dev.bbox.ly",
      "innovativebloodresources.dev.bbox.ly",
      "integratedlabnetwork.dev.bbox.ly",
      "mbc.dev.bbox.ly",
      "nationalcordbloodprogram.dev.bbox.ly",
      "ncbb.dev.bbox.ly",
      "ncbgg.dev.bbox.ly",
      "ncbp2.dev.bbox.ly",
      "nybloodcenter.dev.bbox.ly"
    ]
  }
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
      name                                         = "internal"
      primary                                      = true
      subnet_id                                    = azurerm_subnet.vmss.id
      application_gateway_backend_address_pool_ids = "${azurerm_application_gateway.loadbalancer.backend_address_pool[*].id}"
    }
  }

  tags = {
    Name        = "${var.environment}-cert-${var.project}"
    Environment = var.environment
  }
}

resource "azurerm_key_vault_access_policy" "cert" {
  key_vault_id = "/subscriptions/8de7edc2-0e4f-4e75-876b-0826d65306f9/resourceGroups/wordpress-web-dev/providers/Microsoft.KeyVault/vaults/kv-ssl-cert-w5hb"
  tenant_id    = "27be78cb-b4ab-4113-bccf-26f0d9c570b0"
  object_id    = 0c1ca8a6-6094-498a-b741-13fbdde4e6e0

  key_permissions = [
    "Get", "List",
  ]

  secret_permissions = [
    "Get", "List",
  ]
}



resource "azurerm_application_gateway" "loadbalancer" {
  name                = "${var.environment}-${var.project}-ag"
  resource_group_name = azurerm_resource_group.vmss.name
  location            = azurerm_resource_group.vmss.location

  sku { # @todo
    name     = "Standard_v2"
    tier     = "Standard_v2"
    capacity = 2
  }

  identity {
    type         = "UserAssigned"
    identity_ids = [azurerm_user_assigned_identity.uai.id]
  }

  gateway_ip_configuration {
    name      = "my-gateway-ip-configuration"
    subnet_id = azurerm_subnet.vmss.id
  }

  frontend_port {
    name = local.frontend_http_port_name
    port = 80
  }

  frontend_port {
    name = local.frontend_https_port_name
    port = 443
  }

  frontend_ip_configuration {
    name                 = local.frontend_ip_configuration_name
    public_ip_address_id = azurerm_public_ip.vmss.id
  }

  backend_address_pool {
    name = local.backend_address_pool_name
  }

  backend_http_settings { #@todo: Confirm that this is the correct way to talk to the back-end instance
    name                  = local.http_setting_name
    cookie_based_affinity = "Disabled"
    path                  = ""
    port                  = 80
    protocol              = "Http"
    request_timeout       = 60
  }

  http_listener {
    name                           = local.listener_http_name
    frontend_ip_configuration_name = local.frontend_ip_configuration_name
    frontend_port_name             = local.frontend_http_port_name
    protocol                       = "Http"
  }

  http_listener {
    name                           = local.listener_https_name
    frontend_ip_configuration_name = local.frontend_ip_configuration_name
    frontend_port_name             = local.frontend_https_port_name
    protocol                       = "Https"
    ssl_certificate_name           = "wildcard-dev-nybc-wordpress-bbox-ly" #@todo: VARIABLIZE THIS
    #require_sni                    = true
    ssl_profile_name = local.ssl_profile_name
  }

  request_routing_rule {
    name                       = local.request_routing_rule_name
    rule_type                  = "Basic"
    http_listener_name         = local.listener_http_name
    backend_address_pool_name  = local.backend_address_pool_name
    backend_http_settings_name = local.http_setting_name
  }

  ssl_profile {
    name = local.ssl_profile_name
    ssl_policy {}
  }

  ssl_certificate {
    name                = "wildcard-dev-nybc-wordpress-bbox-ly"
    key_vault_secret_id = "https://kv-ssl-cert-w5hb.vault.azure.net/secrets/wildcard-dev-nybc-wordpress-bbox-ly/a71dc4d7f54f4179824ac9b6c6a9c5e3"
  }
}

