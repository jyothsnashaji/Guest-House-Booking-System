function toggle_collapse(op) {
    var ele = document.getElementById("collapse" + op);
    if (ele.classList.contains('show')) {
        ele.classList.remove('show');
    } else {
        ele.classList.add('show');
    }

}

function openCancelModal(op)
{
    document.getElementById('cancelModal').style.display = 'block';
    document.getElementById('cancel').action='cancel_booking.php?booking_id='+op;

}

function setDatetime(op) {
    var now = new Date();
    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);
    var today = now.getFullYear() + "-" + (month) + "-" + (day);
    document.getElementById(op+'date').value = today;
    var currentTime = ((now.getHours() < 10)?"0":"") + now.getHours() +":"+ ((now.getMinutes() < 10)?"0":"") + now.getMinutes();
    document.getElementById(op+'time').value = currentTime;
  }

function openCheckModal(op,id) {
    setDatetime(op);
    document.getElementById(op+'Modal').style.display = 'block';
    document.getElementById(op+'booking_id').value=id;
  }



function display_subdropdown() {

    var select = document.getElementById("rooms_dropdown");
    var select_sub = document.getElementById("rooms_subdropdown");
    for (var i = 0; i < select_sub.options.length;)
        select_sub.options[i] = null;

    if (select.options[select.selectedIndex].value == "type") {
        select_sub.options[0] = new Option('Deluxe', 'deluxe');
        select_sub.options[1] = new Option('Super Deluxe', 'super deluxe');
        select_sub.options[2] = new Option('VIP', 'vip');
    } else if (select.options[select.selectedIndex].value == "ac") {
        select_sub.options[0] = new Option('AC', 'ac');
        select_sub.options[1] = new Option('Non AC', 'nonac');

    } else if (select.options[select.selectedIndex].value == "capacity") {

        select_sub.options[0] = new Option('1', '1');
        select_sub.options[1] = new Option('2', '2');
        select_sub.options[2] = new Option('3', '3');

    }





}

function activateTab(current) {

    var all = document.getElementsByClassName("nav-link");
    for (var i = 0; i < all.length; ++i) {
        if (all[i].classList.contains('active'))
            all[i].classList.remove('active');
        if (all[i].id == current)
            all[i].classList.add("active");
    }


}

function validateForm(currentTab) {


    var x, y, i, valid = true;
    x = document.getElementsByClassName("form-tab");
    y = x[currentTab].getElementsByTagName("input");
    // A loop that checks every input field in the current tab:


    for (i = 0; i < y.length; i++) {

        // If a field is empty...
        if (y[i].value == "") {
            if (!currentTab && document.getElementsByName('purpose')[0].value == "Personal" && y[i].id == 'purpose-desc')
                continue;
            // add an "invalid" class to the field:
            y[i].className += " is-invalid";
            // and set the current valid status to false
            valid = false;
        }
    }
    // If the valid status is true, mark the step as finished and valid:

    if(!currentTab)
    {
        if (document.getElementsByName("roomsno")[0].classList.contains("is-invalid"))
           valid=false;
    }

    if (currentTab)
    {
        if (document.getElementsByName('contact[]')[currentTab-1].classList.contains("is-invalid"))
            valid=false;
    }
    
    if (valid) {
        document.getElementsByClassName("step")[currentTab].className += " finish";
    }

    return valid;
    // return the valid status
}

function validateRooms(max_guests_per_room,max_rooms,free)
{

    var rooms = document.getElementsByName("roomsno")[0].value;

    var guests = document.getElementsByClassName("step").length - 1;

    if (guests / rooms > max_guests_per_room) 
    {
        if (!document.getElementsByName("roomsno")[0].classList.contains("is-invalid"))
            document.getElementsByName("roomsno")[0].classList.add("is-invalid");
        document.getElementById('roomnumwarning').innerHTML= 'Rooms are insufficient';
        return;
    }
    else if ( guests / rooms < 1) {
        if (!document.getElementsByName("roomsno")[0].classList.contains("is-invalid"))
            document.getElementsByName("roomsno")[0].classList.add("is-invalid");
        document.getElementById('roomnumwarning').innerHTML= 'Rooms are surplus';
        return;
    }
    
    if (rooms>free)
    {
        if (!document.getElementsByName("roomsno")[0].classList.contains("is-invalid"))
            document.getElementsByName("roomsno")[0].classList.add("is-invalid");
        document.getElementById('roomnumwarning').innerHTML= 'Rooms Unavailable';
        return;

    }
    else if (rooms>max_rooms)
    {
        if (!document.getElementsByName("roomsno")[0].classList.contains("is-invalid"))
            document.getElementsByName("roomsno")[0].classList.add("is-invalid");
        document.getElementById('roomnumwarning').innerHTML= 'Limit Exceeded';
        return;

    }
   

    if (document.getElementsByName("roomsno")[0].classList.contains("is-invalid"))
            document.getElementsByName("roomsno")[0].classList.remove("is-invalid");
    return true;
}

function validateContact(currentTab)
{
    var tel=document.getElementsByName('contact[]')[currentTab];
    if(!tel.value.match(/^(0|91)?[1-9][0-9]{9}$/))
    {
        if ( !document.getElementsByName('contact[]')[currentTab].classList.contains("is-invalid"))
            document.getElementsByName('contact[]')[currentTab].classList.add("is-invalid");
        return false;
    }

    if (document.getElementsByName('contact[]')[currentTab].classList.contains("is-invalid"))
            document.getElementsByName('contact[]')[currentTab].classList.remove("is-invalid");
    return true;

    
}

function parseDate(str) {
    var mdy = str.split('-');
    return new Date(mdy[0], mdy[1]-1, mdy[2]);
}

function dateDiff(first, second) {
   

    return Math.round((parseDate(second)-parseDate(first))/(1000*60*60*24));
}


function validateCheckin(prior)

{
    var checkin = document.forms["book_form"]["checkin"].value;
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = yyyy + '-' + mm + '-' + dd;

    datediff= dateDiff(today,checkin);
    if (checkin <= today ||datediff>prior) {
        //alert("Invalid checkin date");
        if (!document.forms["book_form"]["checkin"].classList.contains('is-invalid'))
            document.forms["book_form"]["checkin"].classList.add('is-invalid');
        if (datediff>prior)
            document.getElementById("checkinInvalid").innerHTML="Bookings can be made only "+prior+" days prior";
        return false;
    }


    if (document.forms["book_form"]["checkin"].classList.contains('is-invalid'))
        document.forms["book_form"]["checkin"].classList.remove('is-invalid');
   
    return true;
}

function validateCheckout(limit)

{
    var checkin = document.forms["book_form"]["checkin"].value;
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = yyyy + '-' + mm + '-' + dd;

    var checkout = document.forms["book_form"]["checkout"].value;

    if(!checkout && !checkin)
    {
        if (document.forms["book_form"]["checkout"].classList.contains('is-invalid'))
        document.forms["book_form"]["checkout"].classList.remove('is-invalid');

        if (document.forms["book_form"]["checkin"].classList.contains('is-invalid'))
        document.forms["book_form"]["checkin"].classList.remove('is-invalid');
        return;

    }
    datediff=dateDiff(checkin,checkout);
    if (checkout <= today || checkin >= checkout || datediff > limit ) {
        if (!document.forms["book_form"]["checkout"].classList.contains('is-invalid'))
        document.forms["book_form"]["checkout"].classList.add('is-invalid');

        if(datediff>limit)
        document.getElementById("checkoutInvalid").innerHTML="Bookings can be made only for "+limit+" days";


        return false;
    }
    
    if (document.forms["book_form"]["checkout"].classList.contains('is-invalid'))
        document.forms["book_form"]["checkout"].classList.remove('is-invalid');

    return true;
}

function validateDates()
{
    if (document.forms["book_form"]["checkin"].classList.contains('is-invalid') || 
     (document.forms["book_form"]["checkout"].classList.contains('is-invalid')))
     {
        return false;
     }
     return true;
}

// Display the current tab



function showTab(n) {
    // This function will display the specified tab of the form...
    var x = document.getElementsByClassName("form-tab");
    x[n].style.display = "block";
    //... and fix the Previous/Next buttons:
    if (n == 0) {
        document.getElementById("prevBtn").style.display = "none";
    } else {
        document.getElementById("prevBtn").style.display = "inline";
    }
    if (n == (x.length - 1)) {
        document.getElementById("nextBtn").innerHTML = "Submit";
    } else {
        document.getElementById("nextBtn").innerHTML = "Next";
    }
    //... and run a function that will display the correct step indicator:
    fixStepIndicator(n);
}




function fixStepIndicator(n) {
    // This function removes the "active" class of all steps...
    var i, x = document.getElementsByClassName("step");
    for (i = 0; i < x.length; i++) {
        x[i].className = x[i].className.replace(" active", "");
    }
    //... and adds the "active" class on the current step:
    x[n].className += " active";
}

function confirmBook(n) {
    var details = `<div class='row justify-content-center'>
                     <div class='col'>
                        <b>Number of rooms required: </b>
                        ${document.getElementsByName('roomsno')[0].value}
                     </div>
                     <div class='col'>
                        <b>  Purpose: </b>
                        ${document.getElementsByName('purpose')[0].value} 
                    </div>
                    <div class='col'>
                    <b>   Payment Method: </b>
                    ${document.getElementsByName('payment')[0].value}
                    </div>
                </div>
                <div class='row justify-content-center'>
                        <b>GUESTS</b> 
                </div>`;

    var i;

    for (i = 0; i < n; ++i) 
    { details = details + "<div  class='row justify-content-center'> " + "<div class='col'> " + document.getElementsByName("name[]")[i].value + "</div> " + "<div class='col'> " + document.getElementsByName("rel[]")[i].value + "</div> " + "<div class='col'> " + document.getElementsByName("contact[]")[i].value + "</div> " + "</div> "; }
    document.getElementById("guests_info").innerHTML = details ;
    document.getElementById('confirm_modal').style.display = 'block';
}

function confirmModal(ele)
{
    document.getElementById('guests_form').submit();
    ele.disabled=true;
    document.getElementById('editButton').disabled=true;
}

function displayTable() {

    searchby = document.getElementById('searchby');
    searchby=searchby.options[searchby.selectedIndex].value;
    searchfor = document.getElementById('searchfor').value;
    filter = searchfor.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByClassName(searchby)[0];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}