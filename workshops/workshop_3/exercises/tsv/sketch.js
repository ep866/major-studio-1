function setup() {
    createCanvas(windowWidth, windowHeight);
    loadTable("groceries.tsv", "tsv", "header", showData);
}

function showData(data) {
    var rows = data.getRowCount();
    var lineHeight = height/rows;

    console.log("data ", rows);

    for(var i=0; i<rows; i++) {
        var amount = data.get(i, 0);
        var unit = data.get(i,1);
        var item = data.get(i,2);
        var source = data.get(i,3);


        if(source == "market") {
            fill(255,0,0);
        } else {
            fill(0)
        }

        var thisItem = (amount + " " + unit + " " + source + " " + source);
        var margin = 30;

        text(thisItem, width / 2, margin + lineHeight*i);
    }

}
