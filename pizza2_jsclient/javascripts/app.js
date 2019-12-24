"use strict";
// avoid warnings on using fetch and Promise --
/* global fetch, Promise */
// use port 80, i.e., apache server for webservice execution 
var getUrl = window.location;
var parentUrl = getUrl .protocol + "//" + getUrl.host + "/" ;
var baseUrl = parentUrl + "pizza2_server/api";
// globals representing state of data and UI
let selectedUser = 'none';
let sizes = [];
let toppings = [];
let users = [];
let orders = [];
let main = function () {//(sizes, toppings, users, orders) {
    setupTabs();  // for home/order pizza and meat/meatless
    // for home tab--
    displaySizesToppingsOnHomeTab();
    setupUserForm();
    setupRefreshOrderListForm();
    setupAcknowledgeForm();
    displayOrders();
    // for order tab--
    setupOrderForm();
    displaySizesToppingsOnOrderForm();
};

function displaySizesToppingsOnHomeTab() {
    console.log("displaySizesToppingsOnHomeTab");
    let $sizesSpot = $("#sizes");
    sizes.forEach(function (size) {
        console.log('size = ', size);
        let $li = $("<li class=horizontal>");
        $li.text(size['size'] + " ");
        $sizesSpot.append($li);
    });
    let $toppingsSpot = $("#toppings");
    toppings.forEach(function (topping) {
        let $li = $("<li class=horizontal>");
        $li.text(topping['topping'] + " ");
        $toppingsSpot.append($li);
    });
    // find right elements to build lists in the HTML
    // loop through sizes, creating <li>s for them
    // with class=horizontal to get them to go across horizontally
    // similarly with toppings
}

function setupUserForm() {
    let userlist = users.map(e => e.username);
    console.log('users = ', users);
    let $select = $("#userselect");
    $select.append('<option value="none">none</option>');
    userlist.forEach(function (user) {
        $select.append('<option value="' + user + '"' +
                (user === selectedUser ? ' selected="selected"' : "") +
                '> ' + user + ' </option>');
    });
    $("#userform input").on("click", function () {
        selectedUser = $select.val();
        $("#username-fillin1").text(selectedUser);
        $("#username-fillin2").text(selectedUser);
        if ($select.val() !== 'none') {
            $("#order-area").addClass("active");  // now display orders if any
        }
        $("#orderform")[0].reset();  // clear out prev user choices
        console.log("selected user = " + selectedUser);
        displayOrders(orders);
        return false;
    });
    // find the element with id userselect
    // create <option> elements with value = username, for
    // each user with the current user selected, 
    // plus one for user "none".
    // Add a click listener that finds out which user was
    // selected, make it the "selectedUser", and fill it in the
    //  "username-fillin" spots in the HTML.
    //  Also change the visibility of the order-area
    // and redisplay the orders
}
function setupAcknowledgeForm() {
    console.log("setupAckForm...");
    document.querySelector("#ackform input").addEventListener("click", function () {
        // $("#ackform input").on("click", function () {
        console.log("ack by user = " + selectedUser);
        orders.forEach(function (order) {
            if (order.username === selectedUser && order.status === 'Baked') {
                console.log("Found baked order for user " + order.username);
                order.status = 'Finished';
                updateOrder(order); // post update to server
            }
        });
        displayOrders(orders);
        return false;
    });
}
function setupRefreshOrderListForm() {
    console.log("setupRefreshForm...");
    document.querySelector("#refreshbutton input").addEventListener("click", function () {
        //  $("#refreshbutton input").on("click", function () {
        console.log("refresh orders by user = " + selectedUser);
        getOrders();
        return false;
    });
}
function displayOrders() {
    console.log("displayOrders");
        console.log("displayOrders");
    $("#order-area").removeClass("active");
    if (selectedUser === "none")
        return;  // don't work with orders
    $("#ordertable").empty();
    $("#order-area").addClass("active");  // show orders if any
    console.log("displayOrders orders: ", orders);
    console.log("displayOrders selectedUser: ", selectedUser);
        console.log('users = ', users);
    let user_rec = (users.filter(user => user.username === selectedUser))[0];
    console.log("displayOrders user_rec: ", user_rec);
    let selected_user_id = user_rec.id;
    let userOrders = orders.filter(order => order.user_id === selected_user_id
                      && order.status !== 'Finished');
    //.sort((x,y) => (x.status > y.status)? 1:-1);  // not really needed
    $("#ordermessage").text(": None yet");
    $("#order-info").removeClass("active");
    $("#ackform").removeClass("active");
    if (userOrders.length > 0) {
        $("#order-info").addClass("active");
        $("#ordermessage").text("");
        let $table = $("#ordertable");
        $table.empty();
        let $tr = $("<tr> <th>Order ID</th>" +
                "<th>Size</th>" +
                "<th>Toppings</th>" +
                "<th>Status</th>" +
                " </tr>");
        $table.append($tr);
        userOrders.forEach(function (order) {
            let $tr = $("<tr>");
            $tr.append($("<td>").text(order.id));
            $tr.append($("<td>").text(order.size));
            $tr.append($("<td>").text(order.toppings.toString()));
            $tr.append($("<td>").text(order.status));
            $table.append($tr);
        });
        if (userOrders[0].status === 'Baked') { // if oldest order is Baked
            // show acknowledge-receipt button
            $("#ackform").addClass("active");
        }
    }
}

function setupTabs() {
    console.log("starting setupTabs");
    $(".tabs a span").toArray().forEach(function (element) {
        let $element = $(element);
        $element.on("click", function () {
            // set class=active on selected element (changes background color)
            element.classList.add("active");
            console.log("TAB clicked: " + $element.text());
            let childno = $element.parent().index();  // 0 or 1
            console.log("childno = " + childno);
            let other_tab = childno === 0 ? // The tab to make inactive
                    $element.parent().next().children()[0] :
                    $element.parent().prev().children()[0];
            other_tab.classList.remove("active");
            // Set class=active on the appropriate content element to make
            // it show up. e.g., if $element is <span> of first <a> in tabs,
            // make first content element active. That element is a sibling 
            // of <span>'s grandparent
            let $related_content = childno === 0 ? // up two levels and over...
                    $element.parent().parent().next() :
                    $element.parent().parent().next().next();
            let $other_content = childno === 0 ? // up two levels and over...
                    $element.parent().parent().next().next() :
                    $element.parent().parent().next();

            $related_content.addClass("active");
            $other_content.removeClass("active");
            $("#order-message").text("");  // clean up old message
            refreshData(()=> console.log("refreshed data"));
            return false; // don't do network request
        });
    });
    
    
}

    // Do this last. You may have a better approach, but here's one
    // way to do it. Also edit the html for better initial settings
    // of class active on these elements.
    // Find <span> elements inside <a>'s inside elements with class tabs
    // and process them as follows:  (there are four of them)
    // add a click listener to the element. When a click happens,
    // add class "active" to that element, and figure out this element's
    // parent's (the parent is an <a>) position among its siblings. If it
    // is the first child, the other <a> is its next sibling, and the other
    // <span> is the first child of that <a>. Similarly in the other case.
    // Remove class active from that other tab.
    // Now find the related tabContent element. It's the <span>'s
    // grandparent's next sibling, or sibling after that. Add class active
    // to the newly active one and remove it from the other one.

function displaySizesToppingsOnOrderForm() {
    console.log("displaySizesToppingsOnOrderForm");
    let $sizesSpot = $("#order-sizes");
    sizes.forEach(function (size) {
        console.log('size = ', size);
        let $input = 
                $("<input type='radio' name='pizza_size' required='required'>");
        $input.attr("value", size['size']);
        let $label = $("<label>");
        $label.text(size['size']);
        $sizesSpot.append($input);
        $sizesSpot.append($label);
    });
    let $meatsSpot = $("#meats");
    let $meatlessesSpot = $("#meatlesses");
    toppings.forEach(function (topping) {
        console.log('topping = ', topping);
        var $input = $("<input type='checkbox' name='pizza_topping'>");
        $input.attr("value", topping['topping']);
        var $label = $("<label>");
        $label.text(topping['topping']);
        if (topping['is_meat'] === '1') {
            $meatsSpot.append($input);
            $meatsSpot.append($label);
        } else {
            $meatlessesSpot.append($input);
            $meatlessesSpot.append($label);
        }
    });
    // find the element with id order-sizes, and loop through sizes,
    // setting up <input> elements for radio buttons for each size
    // and labels for them too
    // Then find the spot for meat toppings, and meatless ones
    // and for each create an <input> element for a checkbox
    // and a <label> for each

}

function setupOrderForm() {
    console.log("setupOrderForm...");
    $("#orderform .submitbutton").on("click", function () {
        console.log("saw form click");
        let sizeName = 
                $("#order-sizes input:radio[name='pizza_size']:checked").val();
        console.log("sizeName = " + sizeName);
        let toppings = 
              $('input[type=checkbox]:checked').toArray().map(elt => elt.value);
        console.log("toppings: %O", toppings);
        if (sizeName === undefined || toppings === undefined) {
            $("#order-message").text("Please select size and toppings above");
        } else {
            console.log('users: %O',users);
            console.log('selectedUser '+selectedUser);
            let user_rec = (users.filter(user => user.username === selectedUser))[0];
            console.log('user_rec %O', user_rec);
            let order = 
                    {"user_id": user_rec["id"], "size": sizeName, 
                        "day": 1, "status": "Preparing", "toppings": toppings};
            console.log("order: %O", order);
            postOrder(order, function (newOrder)
            {
                console.log("Your order number is " + newOrder.id);
                $("#order-message").text("Your order number is " + newOrder.id);
                orders.push(newOrder);
                displayOrders();
            });
        }
        return false;  // not executed
    });
    // find the orderform's submitbutton and put an event listener on it
    // When the click event comes in, figure out the sizeName from
    // the radio button and the toppings from the checkboxes
    // Complain if these are not specified, using order-message
    // Else, figure out the user_id of the selectedUser, and
    // compose an order, and post it. On success, report the
    // new order number to the user using order-message

}

// Plain modern JS: use fetch, which returns a "promise"
// that we can combine with other promises and wait for all to finish
function getSizes() {
    let promise = fetch(
            baseUrl + "/sizes",
            {method: 'GET'}
    )
            .then(response => response.json())  // successful fetch
            .then(json => {
                console.log("back from fetch: %O", json);
                sizes = json;
            })
            .catch(error => console.error('error in getSizes:', error));
    return promise;
}
// JQuery/Ajax: for use with $.when: return $.ajax object
function getSizes0() {
    return $.ajax({
        url: baseUrl + "/sizes",
        type: "GET",
        dataType: "json",
        //  headers: {"Content-type":"application/json"}, // needed
        success: function (result) {
            console.log("We did GET to /sizes");
            console.log(result);
            sizes = result;
        }
    });
}

function getToppings() {
    let promise = fetch(
            baseUrl + "/toppings",
            {method: 'GET'}
    )
            .then(response => response.json())
            .then(json => {
                console.log("back from fetch: %O", json);
                toppings = json;
            })
            .catch(error => console.error('error in getToppings:', error));
    return promise;
}

function getToppings0() {
    return $.ajax({
        url: baseUrl + "/toppings",
        type: "GET",
        dataType: "json",
        //  headers: {"Content-type":"application/json"}, // needed
        success: function (result) {
            console.log("We did GET to /toppings");
            console.log(result);
            toppings = result;
        }
    });
}

function getUsers() {
    let promise = fetch(
            baseUrl + "/users",
            {method: 'GET'}
    )
            .then(response => response.json())
            .then(json => {
                console.log("back from fetch: %O", json);
                users = json;
            })
            .catch(error => console.error('error in getUsers:', error));
    return promise;
}

function getOrders() {
    let promise = fetch(
            baseUrl + "/orders",
            {method: 'GET'}
    )
            .then(response => response.json())
            .then(json => {
                console.log("back from fetch: %O", json);
                orders = json;
            })
            .catch(error => console.error('error in getOrders:', error));
    return promise;
}
function updateOrder(order) {
    return $.ajax({
        url: baseUrl + "/orders/" + order.id,
        type: "PUT",
        dataType: "json",
        data: JSON.stringify(order),
        headers: {"Content-type": "application/json"}, // needed
        success: function (result) {
            console.log("We did PUT to /orders/" + order.id);
            console.log("data: " + JSON.stringify(order))
            console.log(result);
        }
    });
}
function postOrder(order, onSuccess) {
    $.ajax({
        url: baseUrl + "/orders",
        type: "POST",
        dataType: "json",
        data: JSON.stringify(order),
        headers: {"Content-type": "application/json"}, // needed
        success: function (result) {
            console.log("We did POST to /orders");
            console.log(result);
            onSuccess(result); // do caller-specified action
        }
    });
}
function refreshData(thenFn) {
    // wait until all promises from fetches "resolve", i.e., finish fetching
    Promise.all([getSizes(), getToppings(), getUsers(), getOrders()]).then(thenFn);
    // JQuery way: wait for all these Ajax requests to finish
    // $.when(getSizes(), getToppings(), getUsers(), getOrders()).done(function (a1, a2, a3, a4) {
    //     thenFn();
    //});
}

$(document).ready(function () {
   console.log("starting...");
   refreshData(main);
});
