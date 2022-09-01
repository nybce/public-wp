resource "azurerm_resource_group" "vmss" {
  name     = "${var.project}-${var.environment}"
  location = var.azure_region
}

resource "azurerm_role_assignment" "access" {
  scope                = azurerm_resource_group.vmss.id
  role_definition_name = "Contributor"
  principal_id         = "0e57e1d8-8d66-45f4-9455-fc584b103c3f"
}
