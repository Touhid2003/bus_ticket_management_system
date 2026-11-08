$(document).ready(function () {

    // Show Form
    $("#openForm").click(function () {
        $("#formOverlay").css("display", "flex").hide().fadeIn(200);
    });

    // Close Form
    $("#closeForm, #cancelForm").click(function () {
        $("#formOverlay").fadeOut(200);
    });

    // Click outside to close
    $("#formOverlay").click(function (event) {
        if (event.target === this) {
            $("#formOverlay").fadeOut(200);
        }
    });

});