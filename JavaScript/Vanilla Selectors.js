//Various vanilla JavaScript selectors

//By ID
document.getElementById('example'); //Gets #example element

//By Tag
document.getElementsByTagName('p'); //Gets all <p> elements

//By Name
document.getElementsByName('first_name'); //Gets <input type="text" name="first_name" />
//To change the value, for example, use something like this:
document.getElementsByName('first_name')[0].value = 'Chris';

//By Class
//Does not work in IE8 or earlier
document.getElementsByClassName('example'); //Gets .example elements

//Query Selector
//This isn't as powerful as the others. It can't change the value of input fields, for example.
document.querySelectorAll('p.example'); //Gets <p class="example">Testing</p>


//https://www.w3schools.com/js/js_htmldom_elements.asp
