function setup(){
    createCanvas(windowWidth, windowHeight);
	loadTable("data/LaborInNonAgricultSector.tsv", "tsv", "header", showData);
}

function showData(data) {
    console.log(data.get(7, 3));

    var rows = data.getRowCount();
    var cols = data.getColumnCount();

    console.log("rows ", rows, " cols ", cols);

    var val, row, col, min, max;

    min = 44;
    max = 0;

    fill(0);

    // get min and max
    for(row=0; row<rows; row++) {
        for(col=3; col<cols; col++) {
            val = data.get(row, col);
            val = float(val);

            if(val > max) {
                max = val;
            }
            if(val < min) {
                min = val;
            }

        }
    }

    console.log("min is ", min);
    console.log("max is ", max);

    // draw scaled vals
    for(row=0; row<rows; row++) {
        noFill();
       // stroke(1);
        beginShape();

        for(col=3; col<cols; col++) {
            val = data.get(row, col);
            val = float(val);

            vertex(
                (width/cols)*col,
                map(val, min, max, height, 0)
            );

            ellipse(
                (width/cols)*col,
                map(val, min, max, height, 0),
                10,
                10
            );

            text(val, ((width/cols)*col + 10),
                map(val, min, max, height, 0))
        }
         endShape();
    }

}
