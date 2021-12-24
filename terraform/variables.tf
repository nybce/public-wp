variable "azure_region" {
  default = "East US"
}

variable "environment" {
  description = "environment"
  default     = "dev"
}

variable "project" {
  description = "project name"
  default     = "nybc-dev-wordpress"
}

variable "database_username" {
  description = "Database username"
}

variable "database_password" {
  description = "Database passwords"
}

variable "database_engine_version" {
  description = "The database engine version"
  default     = "10"
}

variable "database_allocated_storage" {
  type    = number
  default = 20
}

variable "ansible_vault_pass" {
  description = "Ansible vault pass for decrypting env file"
}

variable "nginx_auth_basic" {
  description = "Enable/disable nginx auth basic"
  default     = "on"
}

variable "application_port" {
  description = "The port that you want to expose to the external load balancer"
  default     = 8000
}

variable "docker_image_tag" {
  description = "docker image tag to be used"
  default     = "dev"
}

variable "docker_registry_host" {
  description = "docker registry"
  default     = "docker.io"
}

variable "docker_registry_username" {
  description = "docker registry username"
}

variable "docker_registry_password" {
  description = "docker registry username"
}

variable "nybc_sites" {
  default = [
    "comprehensivecellsolutions", "delmarvablood", "innovativebloodresources",
    "integratedlabnetwork", "mbc", "nationalcordbloodprogram", "ncbb", "ncbgg",
    "ncbp2", "nybloodcenter"
  ]
}

variable "cloudflare_zone_id" {
  description = "the cloudflare zone id for DNS"
  default     = "7360c8be23fe4521002b70f66026aa8c"
}
