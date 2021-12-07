provider "azurerm" {
  # We recommend pinning to the specific version of the Azure Provider you're using
  # since new versions are released frequently
  version = "=2.20.0"

  features {}
}

# the configuration will set using environment variable
provider "cloudflare" {
  version = "~> 2.0"
}
