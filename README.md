# m2-gst-india

Magento 2 module for GST configuration [India]

`comopser require magento-garage/m2-gst-india`  

You'll get a button `Configure GST` in `Store` > `Configuration` > `General` > `Store Information`  
You'll need to select `Country` -> *India* and `Region` for store.  
Your selected region will be base state which will be eligible for CGST/SGST and all other states will be having IGST tax rates configured.

### Things to remeber:
- Only use this button once. (yet to make it reusable.)
- Delete existing tax rates, tax rules and tax classes to use this button again.
