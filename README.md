## QuikWallet Payment Extension for CS Cart

QuikWallet is a payment gateway providing Visa, Master Card, American Express and Netbanking payments.

## Installation
1. Ensure you have latest version of CS Cart installed.
2. Download the zip of this repo.
3. Inside the file downloaded above is a file called 'install.quikwallet.sql'. This has to be executed against your cscart database. You can use phpmyadmin to import the file into your cscart database or copy paste the content and run directly into your mysql shell.
4. Upload rest of the contents of the plugin to your CS Cart Installation directory (content of app folder goes in app folder, content of design folder in design folder).

## Configuration

1. Log into CS-Cart as administrator (http://cscart_installation/admin.php). Navigate to Administration / Payment Methods.
2. Click the "+" to add a new payment method.
3. Choose QuikWallet from the list and then click save. For template, choose "cc_outside.tpl"
4. In Icon field towards bottom of General tab , upload logo.png from this repo
5. Click the 'Configure' tab.
6. Enter your  Partner ID, Partner Secret and QuikWallet Server URL shared with you from QuikWallet.
7. Click 'Save'

Example

url -  https://uat.quikpay.in/api/partner //API domain specified by QuikWallet for Test/Production

partnerid - 75 //Unique id allotted to each merchant

secret -  gA73CzmjhlqfGsJxP7s811ZVmnl70Jky  //256-bit key that to be stored securely


### Support

For support requests or questions email us on support@livquik.com
