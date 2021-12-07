resource "azurerm_mysql_server" "example" {
  name                = "${var.project}-db-${var.environment}"
  location            = azurerm_resource_group.vmss.name
  resource_group_name = azurerm_resource_group.vmss.name

  administrator_login          = var.database_username
  administrator_login_password = var.database_password

  sku_name   = "Standard_B1ms"
  storage_mb = 5120
  version    = "5.7"

  auto_grow_enabled                 = true
  backup_retention_days             = 7
  geo_redundant_backup_enabled      = false
  infrastructure_encryption_enabled = false
  public_network_access_enabled     = true
  ssl_enforcement_enabled           = false

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
}
