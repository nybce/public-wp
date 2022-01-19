output "public_ip" {
  value = azurerm_public_ip.vmss.fqdn
}

output "azure_storage_account_name" {
  value = azurerm_storage_account.vmss.name
}
