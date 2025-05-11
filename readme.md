### registter ####
when check box is false return the we should create the insert all data in table clientUser where we should use the primary key is the phone number. all inputs must valid and are required 

when the check box is true the user inputs must be stored in pharmacistUser where the pharmacy licens number should be the primary key all inputs are required and the must be valid.

nb:check if the primary key already exist and return a pop up in the right corner of  in red with text "user already exist"

### login ###

when use click login and the check box is false return we must check in clientUser has the email with  that password in our database are on the same row the page should direct you to the clientHomepage.html   



when use click login and the check box is tru return we must check in pharmacistUser has the email with  that password in our database are on the same row the page should direct you to the pharmacist.html 


nb:check if there is no primary key already with those information return a pop up in the right corner of  in red with text "invalid email or password"



### add invetory ### 
when user click  add invetory  the  must pop up  a form that has this inputs "Medication	NDC	Category	Stock	Reorder Point	Unit Price	Expiring image(mpg,jpg)" that will be stored in the  sotred in the table medcine where ndc will be numbers and will be the primary key  and when the medicine is less than 20 units it will mark low stock and when the medecine is less than the 5 it will mark the  critical and when the units are 50 it will mark status as normal and when it is above 100 units it will mark overstock  and  if its Expiring date is less than 5days it will create a div that has  

the  background color should be #ffebee and border on the left of 3px #ff5252
<li class="expiry-item expiry-warning">
                            <h4>{Medication}</h4>
                            <div class="expiry-details">
                                <div>Lot:${ndc}</div>
                            <div class="expiry-date">Expires: {date it will expire }</div>
                            </div>

if its Expiring date is less than 10days it will create a div that has  

the  background color should be #fff8e1 and border on the left of 3px #ffc107

<li class="expiry-item expiry-warning">
                            <h4>{Medication}</h4>
                            <div class="expiry-details">
                                <div>Lot:${ndc}</div>
                            <div class="expiry-date">Expires: {date it will expire }</div>

when the pharmasits finish adding the medecine the product if it doesn't exist on the product.html create the product card on the product page .

#### add to cart ###
 when user click add to cart on any of the page on the client pages the product that was clicked will be added on the list of cart items list  on the cart.html page and the amount on the product should be added to the total and when the user click the  and when the user  clicks the remove button the product should be removed from the cart and the total should be updated. when the user clicks on the checkout button it should redirect him to the payment.html page and when he clicks on the cancel button it should redirect him to the clientHomepage.html page. when user click checkout  you should return a pop payment form that will include fields for credit card information, or with mobile phone number and the amount to be paid. when the user clicks on the pay button it should check if the payment is successful or not and if it is successful it should redirect him to the success.html page and if it is not successful it should redirect him to the error.html page. when the user clicks on the cancel button it should redirect him to the clientHomepage.html page. when the user clicks on the print button it should print the invoice and when he clicks on the cancel button it should redirect him to the clientHomepage.html page. when the user clicks on the back button it should redirect him to the cart.html page. when he clicks on the cancel button it should redirect him to the clientHomepage.html page. when he clicks on the back button it should redirect him to the cart.html page. when he clicks on the cancel button it should redirect him to the clientHomepage.html page. when he clicks on the back button it should redirect him to the cart.html page. when he clicks on the cancel button it should redirect him to the clientHomepage.html page. when he clicks on the back button it should redirect him to the cart.html page. when he clicks on the cancel button it should redirect him to the clientHomepage.html page. when he clicks on the back button it should redirect him to the cart.html page. when he clicks on the cancel button it should redirect him to the clientHomepage.html page. when he clicks on the back button it should redirect him to the cart.html page. when he clicks on the cancel button it should redirect him to the clientHomepage.html page. when he clicks on




 #### requirements for client registration ####
- The registration form should include the following fields:
  - First Name
  - Last Name
  - Email
  - Phone Number (Primary Key)
  - Password
  - Address
  - City
  - State
  - Date of Birth
  - Insurance Number
  - Insurance Provider

when user click login and the check box is false return we must check in clientUser has the email with that password in our database are on the same row the page should direct you to the clientHomepage.html

when user click login and the check box is tru return we must check in pharmacistUser has the email with  that password in our database are on the same row the page should direct you to the pharmacist.html
nb:check if there is no primary key already with those information return a pop up in the right corner of  in red with text "invalid email or password"
### requirements for pharmacist registration ####   
- The registration form should include the following fields:
  - First Name
  - Last Name
  - Email
  - Pharmacy License Number (Primary Key)
  - Password
  - Pharmacy Name
  - Address
  - City
  - State
  - Phone Number
  - Date of Birth
  - NPI Number
  - DEA Number
  - Pharmacy Type
  - Pharmacy Address
  - Pharmacy City