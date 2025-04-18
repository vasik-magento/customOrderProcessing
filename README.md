# Vendor_CustomOrderProcessing

## Instructions to Run the App

1. **Install the module**:
   - Copy the module to `app/code`.
   - Run the following commands:

     ```bash
     php bin/magento module:enable Vendor_CustomOrderProcessing
     php bin/magento setup:upgrade
     php bin/magento setup:di:compile
     php bin/magento cache:flush
     ```

2. **API Endpoint**:  
   - **URL**: `POST /rest/V1/customorder/status`
   - **Request Body**:
     ```json
     {
       "increment_id": "100000001",
       "status": "processing"
     }
     ```
   - **Authentication**: Bearer token required.

---

## Architectural Decisions

- **Repository Pattern**: Used Magento's `OrderRepository` to handle order fetching and updating, following Magento’s best practices.
- **Custom API**: Created a REST API for external systems to update order status, ensuring modularity and ease of integration.
- **Order Processing**: As per the order status, the invoice or shipment is created and trigger email notification in case of shipment.
- **Event-Driven**: Utilized the `sales_order_save_after` event to log status changes.
- **Email Notifications**: Shipment emails are sent using the `EmailSender` class to ensure compatibility with future versions of Magento.
