function load_sensor(sensor_tag, warning, okey){
  create_pie_chart(sensor_tag + '_pie_chart', warning, okey);
}

function create_pie_chart(pie_chart_id, warnings, okey) {
  let chart = new Chart(document.getElementById(pie_chart_id), {
      type: 'pie',
      data: {
        labels: ["Right", "Warning"],
        datasets: [{
          backgroundColor: ["#3cba9f","#c45850"],
          data: [okey ,warnings]
        }]
      },
      options: {
        responsive: false,
        maintainAspectRatio: false,
        title: {
          display: true,
        }
      }
  });
}

function open_history(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}

$(document).ready(function() {
    $('.js-example-basic-multiple').select2({
        placeholder: 'This is my placeholder',
        allowClear: true,
    });
});
