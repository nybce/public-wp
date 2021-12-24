resource "cloudflare_record" "web_dev_records" {
  zone_id  = var.cloudflare_zone_id
  proxied  = false
  for_each = toset(var.environment == "dev" ? var.nybc_sites : [])
  name     = each.key == "nybloodcenter" ? "dev.nybc-wordpress" : "${each.key}.dev.nybc-wordpress"
  value    = azurerm_public_ip.vmss.fqdn
  type     = "CNAME"
  ttl      = 1
}

resource "cloudflare_record" "web_staging_records" {
  zone_id  = var.cloudflare_zone_id
  proxied  = false
  for_each = toset(var.environment == "staging" ? var.nybc_sites : [])
  name     = each.key == "nybloodcenter" ? "staging.nybc-wordpress" : "staging.nybc-wordpress.${each.key}"
  value    = azurerm_public_ip.vmss.fqdn
  type     = "CNAME"
  ttl      = 1
}

resource "cloudflare_record" "web_production_records" {
  zone_id  = var.cloudflare_zone_id
  proxied  = false
  for_each = toset(var.environment == "production" ? var.nybc_sites : [])
  name     = each.key == "nybloodcenter" ? "production.nybc-wordpress" : "production.nybc-wordpress.${each.key}"
  value    = azurerm_public_ip.vmss.fqdn
  type     = "CNAME"
  ttl      = 1
}
