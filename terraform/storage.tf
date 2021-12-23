resource "random_string" "account" {
  length  = 18
  special = false
  upper   = false
  number  = true
}

resource "azurerm_storage_account" "vmss" {
  name                     = random_string.account.result
  resource_group_name      = azurerm_resource_group.vmss.name
  location                 = var.azure_region
  account_tier             = "Standard"
  account_replication_type = "ZRS"
  allow_blob_public_access = true
  account_kind             = "StorageV2"

  tags = {
    environment  = var.environment
    project_name = var.project
  }
}

resource "azurerm_storage_container" "vmss" {
  name                  = "storage-${var.project}-${var.environment}"
  storage_account_name  = azurerm_storage_account.vmss.name
  container_access_type = "blob"
}
