resource "azurerm_mysql_server" "example" {
  name                = "${var.project}-db-${var.environment}"
  location            = azurerm_resource_group.vmss.name
  resource_group_name = azurerm_resource_group.vmss.name

  administrator_login          = var.database_username
  administrator_login_password = var.database_password

  sku_name   = "B_Gen5_1"
  storage_mb = 5120
  version    = "5.7"

  auto_grow_enabled                 = true
  backup_retention_days             = 7
  geo_redundant_backup_enabled      = false
  infrastructure_encryption_enabled = false
  public_network_access_enabled     = true
  ssl_enforcement_enabled           = false
}

