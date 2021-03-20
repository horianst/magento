## Cargus Magento module installation manual

### Subscribe to API

- Access https://urgentcargus.portal.azure-api.net/
- Click the 'Sign up' button and fill in the form (you can not use the credentials that the client has for WebExpress)
- Confirm your registration by clicking on the link you received by mail (a real email address should be used)
- On the https://urgentcargus.portal.azure-api.net/developer page, click on `PRODUCTS` in the menu, then`
   UrgentOnlineAPI` and click 'Subscribe', then 'Confirm'
- After the Cargus team confirms subscription to the API, the customer receives a confirmation email
- On the https://urgentcargus.portal.azure-api.net/developer page, click on the user name at the top right, then click
   `Profile '
- The two subscription keys are masked by the characters `xxx ... xxx` and 'Show` in the right of each for display
- It is recommended to use `Primary key` in the Cargus module
   
### Installing the Module

- Copy the module in /app/code
- in a command line go to the project root folder
- run teh command`bin/magento module:status`, the Urgent_Cargus module should be desabled
- to enable it run the command `bin/magento module:enable Urgent_Cargus`
- for the changes to take effect run the following commands: `bin/magento setup:upgrade`, `bin/magento setup:di:compile` and `bin/magento cache:flush`
- Go to `Stores`, `Configuration`, `Sales`, `Shipping Methods`, open the `Cargus` tab, fill in the form and press the orange `Save Config' button at the top right of the page

### Configuring the Module
- Enabled: It is chosen whether the delivery method is active or not
- Ship to Applicable Countries: it is chosen if the delivery method is active for all countries or only for some3. Username: username of the client account in the WebExpress platform
- Ship to Specific Countries: If the delivery method is only active for certain countries, they are chosen here
- Sort Order: a numeric value is added, related to the order between the other active delivery methods
- API Url: https://urgentcargus.azure-api.net/api
- Subscription Key: Primary key obtained in step A. Subscription to API
- Username: the username of the client account in the UrgentOnline / WebExpress platform
- Password: the password for the account mentioned above

### Setting Preferences in app

- Access the tab from the menu `Cargus`, then` Preferences` and fill in the form, then press the orange button `Save preferences` at the bottom of the page
- Pickup Location: one of the available lifting points is chosen. If there is no lift point available, one of the UrgentOnline / WebExpress must be added
- Insurance: choose whether the delivery is with or without insurance
- Saturday Delivery: Choose whether delivery is allowed on Saturdays
- Morning Delivery: Choose if the morning delivery service is used
- Open Package: Choose whether the package opening service is used
- Repayment: Choose the type of repayment - Cash or Bank (Collector Account)
- Payer: Choose the cost of delivery - Sender or Recipinet (Consignee)
- Localities: it is chosen if the dropdowns for localities use the locally saved list or call the service live
- Default shipment type: choose the usual expedition type - Parcel or Envelope (Envelope)
- Free shipping limit: enter the limit for which larger purchases benefit of free shipping (payment of the shipment is
    done automatically to the sender)
- Pickup from Cargus: it is possible to choose whether the package can be picked up by the recipient of the
    nearest Cargus warehouse