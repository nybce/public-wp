module "postgresql" {
  source = "Azure/postgresql/azurerm"

  resource_group_name = azurerm_resource_group.vmss.name
  location            = azurerm_resource_group.vmss.location

  server_name                  = "${var.project}-db-${var.environment}"
  sku_name                     = "B_Gen5_1"
  storage_mb                   = var.database_allocated_storage * 1024
  backup_retention_days        = 7
  geo_redundant_backup_enabled = false
  administrator_login          = var.database_username
  administrator_password       = var.database_password
  server_version               = var.database_engine_version
  ssl_enforcement_enabled      = false
  db_charset   = "UTF8"
  db_collation = "English_United States.1252"

  firewall_rule_prefix = "firewall-"
  firewall_rules = [
    { name = "subnet1", start_ip = "10.0.1.1", end_ip = "10.0.1.254" },
    { name = "subnet2", start_ip = "52.255.162.124", end_ip = "52.255.162.124" },
    { start_ip = "127.0.0.0", end_ip = "127.0.1.0" },
  ]

  tags = {
    environment  = var.environment
    project_name = var.project
  }

  postgresql_configurations = {
    backslash_quote = "on",
  }
}
