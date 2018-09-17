function getCurrentYear(){
  var dt = new Date();
  var year = dt.getYear() + 1900;
  return year;
}

function dump(obj) {
    let out = '';
    for (let i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
    alert(out);
}