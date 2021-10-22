
//Scroll navbar 
$(window).on("scroll", function() {
    if($(window).scrollTop() > 50) {
        $("#header").addClass("active");
    } else {
       $("#header").removeClass("active");
    }
});


//Auto slider 
//query takes 3 second to resolve
function mockQuery () {
    return new Promise((resolve, reject)=> {
    setTimeout(()=> resolve(
       // $("#megabanner .slider-control .slider-right").click()
    ),5000);
  });
  }
  
  async function makeQueryCall(){
    await mockQuery()
    //after resolve it is called again 1/10th of a second later
    setTimeout(()=> makeQueryCall(), 4000);
  }
  
  makeQueryCall(); 