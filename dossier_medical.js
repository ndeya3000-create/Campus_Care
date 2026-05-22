document.getElementById("search").addEventListener("keyup", function(){

let filter = this.value.toLowerCase();
let rows = document.querySelectorAll("#table tr");

rows.forEach((row,i)=>{

if(i===0) return;

let text = row.innerText.toLowerCase();

row.style.display = text.includes(filter) ? "" : "none";

});

});

