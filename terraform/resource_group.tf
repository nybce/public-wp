resource "azurerm_resource_group" "vmss" {
  name     = "${var.project}-${var.environment}"
  location = var.azure_region
}
