# PowerShell script to move packages to new monorepo structure

# Core packages
Move-Item -Path "packages/Core" -Destination "packages/core/Core" -Force
Move-Item -Path "packages/DataGrid" -Destination "packages/core/DataGrid" -Force
Move-Item -Path "packages/DataTransfer" -Destination "packages/core/DataTransfer" -Force
Move-Item -Path "packages/Rule" -Destination "packages/core/Rule" -Force
Move-Item -Path "packages/User" -Destination "packages/core/User" -Force

# Admin packages
Move-Item -Path "packages/Admin" -Destination "packages/admin/Admin" -Force

# Auth packages
Move-Item -Path "packages/SocialLogin" -Destination "packages/auth/SocialLogin" -Force

# Frontend packages
Move-Item -Path "packages/Shop" -Destination "packages/frontend/Shop" -Force
Move-Item -Path "packages/Theme" -Destination "packages/theme/Theme" -Force

# Module packages
Move-Item -Path "packages/Attribute" -Destination "packages/modules/Attribute" -Force
Move-Item -Path "packages/BookingProduct" -Destination "packages/modules/Booking" -Force
Move-Item -Path "packages/CMS" -Destination "packages/modules/CMS" -Force
Move-Item -Path "packages/CartRule" -Destination "packages/modules/CartRule" -Force
Move-Item -Path "packages/CatalogRule" -Destination "packages/modules/CatalogRule" -Force
Move-Item -Path "packages/Category" -Destination "packages/modules/Category" -Force
Move-Item -Path "packages/Checkout" -Destination "packages/modules/Checkout" -Force
Move-Item -Path "packages/Customer" -Destination "packages/modules/Customer" -Force
Move-Item -Path "packages/FPC" -Destination "packages/modules/FPC" -Force
Move-Item -Path "packages/GDPR" -Destination "packages/modules/GDPR" -Force
Move-Item -Path "packages/Inventory" -Destination "packages/modules/Inventory" -Force
Move-Item -Path "packages/MagicAI" -Destination "packages/modules/MagicAI" -Force
Move-Item -Path "packages/Marketing" -Destination "packages/modules/Marketing" -Force
Move-Item -Path "packages/Notification" -Destination "packages/modules/Notification" -Force
Move-Item -Path "packages/Product" -Destination "packages/modules/Product" -Force
Move-Item -Path "packages/Sales" -Destination "packages/modules/Sales" -Force
Move-Item -Path "packages/Sitemap" -Destination "packages/modules/Sitemap" -Force
Move-Item -Path "packages/SocialShare" -Destination "packages/modules/SocialShare" -Force
Move-Item -Path "packages/Tax" -Destination "packages/modules/Tax" -Force

# Payment packages
Move-Item -Path "packages/Payment" -Destination "packages/payment/Payment" -Force
Move-Item -Path "packages/Paypal" -Destination "packages/payment/Paypal" -Force

# Shipping packages
Move-Item -Path "packages/Shipping" -Destination "packages/shipping/Shipping" -Force

Write-Host "Packages have been moved to their new locations."
