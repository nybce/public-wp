locals {
  backend_address_pool_name       = "${var.environment}-${var.project}-be-ap"
  frontend_http_port_name         = "${var.environment}-${var.project}-fe-http-pconf"
  frontend_https_port_name        = "${var.environment}-${var.project}-fe-https-pconf"
  frontend_ip_configuration_name  = "${var.environment}-${var.project}-fe-ipconf"
  http_setting_name               = "${var.environment}-${var.project}-be-htst"
  listener_http_name              = "${var.environment}-${var.project}-http-lstn"
  listener_https_name             = "${var.environment}-${var.project}-https-lstn"
  request_routing_rule_name       = "${var.environment}-${var.project}-rqrt"
  request_routing_rule_https_name = "${var.environment}-${var.project}-https-rqrt"
  redirect_configuration_name     = "${var.environment}-${var.project}-rdrcfg"
  ssl_profile_name                = "${var.environment}-${var.project}-sslprof"
  domain_name_sets = {
    "staging" = [
      "comprehensivecellsolutions.staging.nybc-wordpress.bbox.ly",
      "delmarvablood.staging.nybc-wordpress.bbox.ly",
      "innovativebloodresources.staging..nybc-wordpress.bbox.ly",
      "integratedlabnetwork.staging.nybc-wordpress.bbox.ly",
      "mbc.staging.nybc-wordpress.bbox.ly",
      "nationalcordbloodprogram.staging.nybc-wordpress.bbox.ly",
      "ncbb.staging.nybc-wordpress.bbox.ly",
      "ncbgg.staging.nybc-wordpress.bbox.ly",
      "ncbp2.staging.nybc-wordpress.bbox.ly",
      "nybloodcenter.staging.nybc-wordpress.bbox.ly",
      "nybc-enterprise.staging.nybc-wordpress.bbox.ly"
          ]
  }
  cert_files = {
    "staging" = "kv-ssl-cert-w5hb-wildcard-dev-nybc-wordpress-bbox-ly-20211210.pfx"
  }
}

resource "random_id" "server" {
  keepers = {
    # Generate a new id each time we switch to a new Azure Resource Group
    rg_id = var.project
  }

  byte_length = 4
}


data "local_file" "certificate_data" {
  filename = "certs/${local.cert_files[var.environment]}"
}

data "template_file" "init" {
  template = file("ec2-init.sh.tpl")

  vars = {
    docker_registry_host     = var.docker_registry_host
    docker_registry_username = var.docker_registry_username
    docker_registry_password = var.docker_registry_password
    docker_image_tag         = var.docker_image_tag
    ansible_vault_pass       = var.ansible_vault_pass
    environment              = var.environment
    nginx_auth_basic         = var.nginx_auth_basic
  }
}



resource "azurerm_app_service_plan" "wordpress_service_plan" {
 name                = "${var.project}-${var.environment}-service-plan"
 location            = azurerm_resource_group.vmss.location
 resource_group_name = azurerm_resource_group.vmss.name
 kind                = "Linux"
 reserved            = true

 sku {
   tier     = "Basic"
   size     = "B1"
 }
}

resource "azurerm_app_service" "my_app_service_container" {
 name                    = "${var.environment}-${var.project}"
 location            = azurerm_resource_group.vmss.location
 resource_group_name = azurerm_resource_group.vmss.name
 app_service_plan_id     = azurerm_app_service_plan.wordpress_service_plan.id
 https_only              = true
 client_affinity_enabled = true
 site_config {
     app_command_line = ""
     linux_fx_version = "DOCKER|nybcteam/nybc-wordpress:staging"
 }
 app_settings = {
  "WEBSITES_ENABLE_APP_SERVICE_STORAGE" = "false"
  "DOCKER_REGISTRY_SERVER_URL"          = "${var.docker_registry_host}"
  "DOCKER_REGISTRY_SERVER_PASSWORD"     = "${var.docker_registry_password}"
  "DOCKER_REGISTRY_SERVER_USERNAME"     = "${var.docker_registry_username}"
  "ansible_vault_pass"       = "${var.ansible_vault_pass}"
  "environment"              = "${var.environment}"
  "ANSIBLE_VAULT_PASS"       = "${var.ansible_vault_pass}"
  "ENVIRONMENT"              = "${var.environment}"
 }
}
