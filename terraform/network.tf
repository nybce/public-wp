resource "random_string" "fqdn" {
  length  = 7
  special = false
  upper   = false
  number  = false
}

resource "random_string" "agrs" {
  length  = 8
  special = false
  upper   = false
  number  = false
}


resource "azurerm_virtual_network" "vmss" {
  name                = "vmss-vnet-${var.project}-${var.environment}"
  address_space       = ["10.0.0.0/16"]
  location            = var.azure_region
  resource_group_name = azurerm_resource_group.vmss.name
  tags = {
    Name        = "${var.environment}-vnet-${var.project}"
    Environment = var.environment
  }
}

resource "azurerm_subnet" "vmss" {
  name                 = "vmss-subnet-${var.project}-${var.environment}"
  resource_group_name  = azurerm_resource_group.vmss.name
  virtual_network_name = azurerm_virtual_network.vmss.name
  address_prefixes     = ["10.0.1.0/24"]
  service_endpoints    = ["Microsoft.Storage"]
}

resource "azurerm_subnet" "ag" {
  name                 = "ag-subnet-${var.project}-${var.environment}"
  resource_group_name  = azurerm_resource_group.vmss.name
  virtual_network_name = azurerm_virtual_network.vmss.name
  address_prefixes     = ["10.0.2.0/24"]
}


resource "azurerm_public_ip" "vmss" {
  name                = "vmss-public-ip-${var.project}-${var.environment}"
  location            = var.azure_region
  sku                 = "Standard"
  resource_group_name = azurerm_resource_group.vmss.name
  allocation_method   = "Static"
  domain_name_label   = random_string.fqdn.result
  tags = {
    Name        = "${var.environment}-public-ip-${var.project}"
    Environment = var.environment
  }
}

resource "azurerm_public_ip" "ag" {
  name                = "ag-public-ip-${var.project}-${var.environment}"
  location            = var.azure_region
  resource_group_name = azurerm_resource_group.vmss.name
  allocation_method   = "Static"
  domain_name_label   = random_string.agrs.result
  tags = {
    Name        = "${var.environment}-ag-public-ip-${var.project}"
    Environment = var.environment
  }
}

resource "azurerm_network_security_group" "nsg" {
  name                = "nsg-${var.project}-${var.environment}"
  resource_group_name = azurerm_resource_group.vmss.name
  location            = var.azure_region
  tags = {
    Name        = "${var.environment}-nsg-${var.project}"
    Environment = var.environment
  }
}

resource "azurerm_network_security_rule" "nsg_rule" {
  for_each = {
    ssh = {
      destination_port_range = "22"
      source_port_range      = "*"
      source_address_prefix  = "*"
      priority               = 1
    },
    application = {
      destination_port_range = "8000"
      source_port_range      = "*"
      source_address_prefix  = "*"
      priority               = 2
    }
    applicationssh = {
      destination_port_range = "443"
      source_port_range      = "*"
      source_address_prefix  = "*"
      priority               = 3
    }
  }
  name                        = each.key
  priority                    = 100 * (each.value.priority + 1)
  direction                   = "Inbound"
  access                      = "Allow"
  protocol                    = "Tcp"
  source_port_range           = each.value.source_port_range
  destination_port_range      = each.value.destination_port_range
  source_address_prefix       = each.value.source_address_prefix
  destination_address_prefix  = "*"
  description                 = "Inbound_Port_${each.key}"
  resource_group_name         = azurerm_resource_group.vmss.name
  network_security_group_name = azurerm_network_security_group.nsg.name
  depends_on                  = [azurerm_network_security_group.nsg]
}
