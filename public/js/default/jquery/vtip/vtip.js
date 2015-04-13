/**
*Vertigo Tip by www.vertigo-project.com
*Requires jQuery
*/

this.vtip = function() {
    this.xOffset = -13; // x distance from mouse
    this.yOffset = 22; // y distance from mouse
    

    $(".descTip").unbind().hover(
        function(e) {
            this.t = this.title;
            this.title = '';
            this.top = (e.pageY + yOffset); this.left = (e.pageX + xOffset);

            $('body').append( '<p id="vtip"><img id="vtipArrow" />' + this.t + '</p>' );

            $('p#vtip #vtipArrow').attr("src", '/images/default/vtip_arrow.png');
            $('p#vtip').css("top", this.top+"px").css("left", this.left+"px").fadeIn("slow");

        },
        function() {
            this.title = this.t;
            $("p#vtip").fadeOut("slow").remove();
        }
    ).mousemove(
        function(e) {
            this.top = (e.pageY + yOffset);
            this.left = (e.pageX + xOffset);

            $("p#vtip").css("top", this.top+"px").css("left", this.left+"px");
        }
    );

    $(".descTip").unbind().hover(
        function(e) {
            this.t = this.title;
            this.title = '';
            this.top = (e.pageY + yOffset); this.left = (e.pageX + xOffset);

            $('body').append( '<p id="vtip"><img id="vtipArrow" />' + this.t + '</p>' );

            $('p#vtip #vtipArrow').attr("src", '/images/default/vtip_arrow.png');
            $('p#vtip').css("top", this.top+"px").css("left", this.left+"px").fadeIn("slow");

        },
        function() {
            this.title = this.t;
            $("p#vtip").fadeOut("slow").remove();
        }
    ).mousemove(
        function(e) {
            this.top = (e.pageY + yOffset);
            this.left = (e.pageX + xOffset);

            $("p#vtip").css("top", this.top+"px").css("left", this.left+"px");
        }
    );

};

$(document).ready(function(){vtip();});
