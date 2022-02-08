resource "azurerm_container_registry" "nybc_wordpress_container_registry" {
  name                     = "nybc_wordpress_container_registry"
  resource_group_name      = azurerm_resource_group.vmss.name
  location                 = azurerm_resource_group.vmss.location
  sku                      = "Basic"
  admin_enabled            = true
}

output "admin_password" {
  value       = azurerm_container_registry.nybc_wordpress_container_registry.admin_password
  description = "The object ID of the user"
  sensitive = true
}

output "admin_username" {
  value       = azurerm_container_registry.nybc_wordpress_container_registry.admin_username
  description = "The object ID of the user"
  sensitive = true
}
