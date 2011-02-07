function showError(error) {
    $("#warning").hide();
    $("#warning").html(error);
    $("#warning").fadeIn();
    var t=setTimeout("hideError()", 7000);  
}

function hideError() {
    $("#warning").fadeOut();
}

//Check if user authentication is successful
function checkAuth() {
    $('#submitbutton').unbind('click');
    $("#testwrapper").remove();
    var username = $("#username").val();
    var password = $("#password").val();
    if (username == ""||password == "") {
        showError("You must enter a Username and Password!");
        return -1;
    }
    var jsondata = {"username":username,"password":password};
    $.ajax({
        type: "POST",
        url: "auth/login.php",
        data: {"jsondata":jsondata},
        dataType: "json",
        success: function(jsondata){
            if (jsondata.login == "True") {
                if(jsondata.flags == "admin") {
                    hideError();
                    startAdminTest(jsondata);                
                }
                else{ 
                    hideError();
                    startTest(jsondata);
                }
            }
            else if (jsondata.login == "Restricted" || jsondata.login == "Restricted") {
                showError("Unknown Username or Bad Password!");
                $("#submitbutton").click(function(){checkAuth();});
            }
            else if (jsondata.login == "Server") {
                showError("Unable to Contact Server! " + jsondata.errorCode);  //AD server could not be contacted.
                $("#submitbutton").click(function(){checkAuth();});
            }
            else {
                showError("Unknown Error! Error Code: 002"); //json came back malformed. Login was not True, Restricted, or Error. PHP error.
                $("#submitbutton").click(function(){checkAuth();});
            }
        },
        error: function() {
            showError("Unknown Error! Error Code: 001"); //Ajax did not go through. Check Server.
            $("#submitbutton").click(function(){checkAuth();});
        }
    });
}





//If authentication is successful slide down the test and change the login to reset
function startTest(jsondata) {
    //add base html
    $("#header").after("<div id=\"testwrapper\"><div id=\"warningbox\"><span id=\"warning\"></span></div><div id=\"testbox\"><div><div class=\"centered\"><h1 id=\"name\">Test</h1><div id=\"oses\"><div class=\"oswrapper\"><img id=\"windowsimg\" src=\"images/windows.jpg\" /><div class=\"oscaption\"><p>Windows</p></div></div><div class=\"oswrapper\"><img id=\"macimg\" src=\"images/mac.jpg\" /><div class=\"oscaption\"><p>OS X</p></div></div><div class=\"oswrapper\"><img id=\"linuximg\" src=\"images/linux.jpg\" /><div class=\"oscaption\"><p>Linux</p></div></div></div></div></div></div>");
    $("#linuximg").click(function(){createFiles(jsondata, "Linux");$(this).parent().css("border-color", "#1C94C4").delay(2000).animate({borderColor: "#eee"}, 3000);});
    $("#macimg").click(function(){createFiles(jsondata, "Mac");$(this).parent().css("border-color", "#1C94C4").delay(2000).animate({borderColor: "#eee"}, 3000);});
    $("#windowsimg").click(function(){createFiles(jsondata, "Windows");$(this).parent().css("border-color", "#1C94C4").delay(2000).animate({borderColor: "#eee"}, 3000);});
    //Do animations
    $("#testbox textarea, #testbox, #testwrapper, #formbox, #wrapper, #header, .centered").animate({width: "+=250"});
    $("#header h1").html("Choose Operating System");
    $("#header").css("border-color", "#f00");
    $("#testwrapper").hide();
    $("#name").html(jsondata.data.cn + " (<i>" + jsondata.data.samaccountname + "</i>)");
    $("#testwrapper").slideDown(1000);
    $("#header").animate({"margin-top":"5px"}, 1000);
    $("#formbox").animate({"height":"30px"}, 1000);
    $("#formbox div").fadeOut("slow", function (){
        $("#formbox div").html("<button id=\"resetbutton\">Reset</button>");
        $("#resetbutton").button();
        $("#formbox div").fadeIn("slow");
        $("#resetbutton").click(function() {
            //Reset is pressed. Change everything back to login
            clearTimeout(t);
            $("#header, #wrapper, #formbox, .centered").animate({width: "-=250"});
            $("#header h1").html("Please Log in");
            $("#header").css("border-color", "#1C94C4");
            $("#header").animate({"margin-top":"150px"}, 1000);
            $("#formbox").animate({"height":"132px"}, 1000);
            $("#testwrapper").slideUp(1000);
            $("#testwrapper").remove();
            $("#formbox div").fadeOut("slow", function (){
                $("#formbox div").html("<p>Username:<input id=\"username\" type=\"text\" name=\"username\" /></p><p>Password:<input id=\"password\" type=\"password\" name=\"password\" /></p><p><button id=\"submitbutton\">Submit</button></p>");
                $("#username").keyup(function(e) {if(e.keyCode == 13) {checkAuth();}});
                $("#password").keyup(function(e) {if(e.keyCode == 13) {checkAuth();}});
                $("#submitbutton").button();
                $("#submitbutton").click(function(){checkAuth();});
                $("#formbox div").fadeIn("slow");
                $("#info").fadeIn("slow");
                
            });
            //End reset events
        });
    });
    $("#info").fadeOut("slow");
    var t=setTimeout("loginTimeout();", 110000);
}

//ADMIN If authentication is successful slide down the test and change the login to reset
function startAdminTest(jsondata) {
    //add base html
    $("#header").after("<div id=\"testwrapper\"><div id=\"warningbox\"><span id=\"warning\"></span></div><div id=\"testbox\"><div><div class=\"centered\"><h1 id=\"name\">Test</h1><p class=\"centered\">Username <input type=\"text\" id=\"vpnusername\"></p><p class=\"centered\">Windows<input type=\"radio\" name=\"os\" value=\"Windows\" checked/> Mac<input type=\"radio\" name=\"os\" value=\"Mac\" /> Linux<input type=\"radio\" name=\"os\" value=\"Linux\" /></p></div></div></div>");
    $("#testbox").append("<p class=\"centered\"><button id=\"testsubmitbutton\">Submit</button></p>");
    $("#testsubmitbutton").button();
    $("#testsubmitbutton").click(function(){jsondata.username = $("#vpnusername").val(); createFiles(jsondata, $("input[name=\"os\"]:checked").val());});
    //Do animations
    $("#header h1").html("ADMIN");
    $("#header h1").css("color", "#f00");
    $("#header").css("border-color", "#f00");
    $("#testwrapper").hide();
    $("#name").html("Administrator");
    $("#testwrapper").slideDown(1000);
    $("#header").animate({"margin-top":"5px"}, 1000);
    $("#formbox").animate({"height":"30px"}, 1000);
    $("#formbox div").fadeOut("slow", function (){
        $("#formbox div").html("<button id=\"resetbutton\">Reset</button>");
        $("#resetbutton").button();
        $("#formbox div").fadeIn("slow");
        $("#resetbutton").click(function() {
            //Reset is pressed. Change everything back to login
            clearTimeout(t);
            $("#header h1").html("Please Log in");
            $("#header h1").css("color", "#333");
            $("#header").css("border-color", "#1C94C4");
            $("#header").animate({"margin-top":"150px"}, 1000);
            $("#formbox").animate({"height":"132px"}, 1000);
            $("#testwrapper").slideUp(1000);
            $("#testwrapper").remove();
            $("#formbox div").fadeOut("slow", function (){
                $("#formbox div").html("<p>Username:<input id=\"username\" type=\"text\" name=\"username\" /></p><p>Password:<input id=\"password\" type=\"password\" name=\"password\" /></p><p><button id=\"submitbutton\">Submit</button></p>");
                $("#username").keyup(function(e) {if(e.keyCode == 13) {checkAuth();}});
                $("#password").keyup(function(e) {if(e.keyCode == 13) {checkAuth();}});
                $("#submitbutton").button();
                $("#submitbutton").click(function(){checkAuth();});
                $("#formbox div").fadeIn("slow");
                $("#info").fadeIn("slow")
            });
            //End reset events
        });
    });
    $("#info").fadeOut("slow");
    var t=setTimeout("loginTimeout()", 110000);
}  

//Send Information to Create Certificates and if Yes then download zip
function createFiles(jsondata, os) {
    jsondata.os = os;
    $.ajax({
        type: "POST",
        url: "auth/zipgen.php",
        data: {"jsondata":jsondata},
        dataType: "json",
        success: function(returnData){
            if (returnData.login == "Yes") {
                hideError();
                window.location = "auth/download.php?sessionID="+jsondata.data.sessionID+"&os="+jsondata.os;
            }
            if (returnData.login == "Admin") {
                hideError();
                window.location = "auth/download.php?sessionID="+jsondata.data.sessionID+"&os="+jsondata.os+"&username="+jsondata.username;
            }
            else if (jsondata.login == "No") {
                showError("Problem Creating Files. Please Contact Tech Support");
            }
        }
    });  
}

//confirm send with user
function confirmSubmit(text,jsondata) {
    $("body").append("<div id=\"dialog-confirm\" title=\"Confirmation\"><p>" + text + "</p></div>");
    $("#dialog-confirm").dialog({
        resizable: false,
        modal: true,
        buttons: {
            'Yes': function() {
                $(this).dialog('close');
                $("#dialog-confirm").remove();
                sendTest(jsondata);
            },
            Cancel: function() {
                $(this).dialog('close');
                $("#dialog-confirm").remove();
                return -1;
            }
        }
    });

}

//Show Success Message
function loginTimeout() {
    $("#testwrapper").slideUp(1000);
    $("#testwrapper").remove();
    $("#header").css("border-color", "#1C94C4");
    $("#header").animate({"margin-top":"300px"}, 1000);
    $("#header h1").fadeOut(function(){$("#header h1").html("Auto Logout");$("#header h1").css("color","#1C94C4");$("#header h1").fadeIn();});
}

$(document).ready(function() {
    $("#submitbutton").button();
    $("#submitbutton").click(function(){checkAuth();});
    $("#username").keyup(function(e) {if(e.keyCode == 13) {checkAuth();}});
    $("#password").keyup(function(e) {if(e.keyCode == 13) {checkAuth();}});
});
