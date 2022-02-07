resource "azurerm_container_registry" "nybc_container" {
  name                     = "acrname"
  resource_group_name      = azurerm_resource_group.vmss.name
  location                 = azurerm_resource_group.vmss.id.location
  sku                      = "Basic"
  admin_enabled            = true
}

output "admin_password" {
  value       = azurerm_container_registry.nybc_container.admin_password
  description = "The object ID of the user"
}
