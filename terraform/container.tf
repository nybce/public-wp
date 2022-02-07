resource "azurerm_container_registry" "nybc_container" {
  name                     = "acrname"
  resource_group_name      = azurerm_resource_group.vmss.name
  location                 = azurerm_resource_group.vmss.location
  sku                      = "Basic"
  admin_enabled            = true
  admin_username           = var.docker_registry_username
  admin_password           = var.docker_registry_password
}
