var page = new WebPage();
var system = require("system");
page.paperSize = {
	format: "Letter",
	orientation: "portrait",
	margin: {left:"2.2cm", right:"2.5cm", top:"1cm", bottom:"1cm"},
	footer: {
		height: "0.7cm",
		contents: phantom.callback(function(pageNum, numPages) {
			return "<div style='text-align:center;'><small>" + pageNum + "</small></div>";
		})
	}
};
page.zoomFactor = 1.5;
page.open(system.args[1], function (status) {
	page.render(system.args[2]);
	phantom.exit();
});