<?php ob_start() ?>
  <div class="column-group gutters">
    <div class="large-50 medium-50 small-100" style="height:340px">
      <h2><?php echo _('Nombre de cafés'); ?></h2>
      <figure id="coffeechart"></figure>
    </div>
    <div class="large-50 medium-50 small-100" style="height:340px">
      <h2><?php echo _('Statistiques'); ?></h2>
      <h4 style="font-weight:normal;"><span id="coffees_today" class="ink-badge grey"> <i class="icon-coffee"></i></span> <?php echo _("aujourd'hui"); ?></h4>
      <h4 style="font-weight:normal;"><span id="coffees_month" class="ink-badge grey"> <i class="icon-coffee"></i></span> <?php echo _("ce mois"); ?></h4>
      <h4 style="font-weight:normal;"><span id="money_today" class="ink-badge grey"> <i class="icon-euro"></i></span> <?php echo _("dépensés aujourd'hui"); ?></h4>
      <h4 style="font-weight:normal;"><span id="money_month" class="ink-badge grey"> <i class="icon-euro"></i></span> <?php echo _("dépensés ce mois"); ?></h4>
      <script src="templates/d3/d3.v3.min.js"></script>
      <script type="text/javascript">
        function loadDashboardJSON() {
          d3.json("dashboard.json", function(data) {
            d3.select("#coffees_today").html(data.coffees_today + ' <i class="icon-coffee"></i>');
            d3.select("#coffees_month").html(data.coffees_month + ' <i class="icon-coffee"></i>');
            d3.select("#money_today").html(Math.round(data.money_today) + ' <i class="icon-euro"></i>');
            d3.select("#money_month").html(Math.round(data.money_month) + ' <i class="icon-euro"></i>');
          });
          dashboardTimer = setTimeout(loadDashboardJSON, 10000);
        }
        loadDashboardJSON();
      </script>
    </div>
    <div class="large-50 medium-50 small-100">
      <h2><?php echo _('Prochains évènements'); ?></h2>
      <table class="ink-table">
        <tbody>
          <?php
            $i = 0;
            foreach ($events as $event): ?>
            <tr>
              <td>
                <strong><?php echo $event['title']; ?></strong>
                <div class="small"><i class="icon-calendar"></i> <?php echo str_replace("<", "", str_replace("<br>", "", substr($event['summary'], 7, strpos($event['summary'],"<br>")))); ?></div>
              </td>
            </tr>
            <?php $i++; if($i > 7) break; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="large-50 medium-50 small-100">
      <h2><?php echo _('Messages'); ?></h2>
      <table class="ink-table">
        <tbody>
          <?php 
            $i = 0;
            foreach ($messages as $message): ?>
            <tr>
              <td>
                <strong><?php echo $message['message']; ?></strong>
                <div class="small"><i class="icon-calendar"></i> <?php echo datetime_to_string($message['timestamp']); ?> <i class="icon-user"></i> <?php echo $message['firstname']; ?> </div>
              </td>
            </tr>
            <?php $i++; if($i > 1) break; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
      <h2><?php echo _('Compteurs SmarTB'); ?></h2>
      <div id="iframe">
        <iframe frameborder="0" marginheight="0" marginwidth="0" scrolling="no" src="http://smartb.labo4g.enstb.fr/demo&amp;id=23&amp;embed=1&amp;apikey=9d557961a518fc3f95cb6478e612e520" style="height : 169px; width: 400px;"></iframe>
      </div>
    </div>
  </div><!--/.column-group -->
  <style>
    .axis path,
    .axis line {
      fill: none;
      stroke: #000;
      shape-rendering: crispEdges;
    }

    .bar {
      fill: steelblue;
    }

    .x.axis path {
      display: none;
    }
  </style>
  <script>
    var margin = {top: 40, right: 20, bottom: 30, left: 40},
        width = 400 - margin.left - margin.right,
        height = 300 - margin.top - margin.bottom;

    var x = d3.scale.ordinal()
        .rangeRoundBands([0, width], .1);

    var y = d3.scale.linear()
        .range([height, 0]);

    var xAxis = d3.svg.axis()
        .scale(x)
        .orient("bottom")
        .tickFormat(function(d) { return d + "/13"; });

    var yAxis = d3.svg.axis()
        .scale(y)
        .orient("left")
        .ticks(5);

    var svg = d3.select("#coffeechart").append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
      .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    d3.tsv("coffees.tsv", type, function(error, data) {
      x.domain(data.map(function(d) { return d.month; }));
      y.domain([0, d3.max(data, function(d) { return d.coffees; })]);

      svg.append("g")
          .attr("class", "x axis")
          .attr("transform", "translate(0," + height + ")")
          .call(xAxis);

      svg.append("g")
          .attr("class", "y axis")
          .call(yAxis)
        .append("text")
          .attr("transform", "rotate(-90)")
          .attr("y", 6)
          .attr("dy", ".71em")
          .style("text-anchor", "end")
          .text("<?php echo _('Cafés'); ?>");

      svg.selectAll(".bar")
          .data(data)
        .enter().append("rect")
          .attr("class", "bar")
          .attr("x", function(d) { return x(d.month); })
          .attr("width", x.rangeBand())
          .style("fill", function(d) { return d3.rgb(128, 128, 128); })
          .attr("y", function(d) { return y(d.coffees); })
          .attr("height", function(d) { return height - y(d.coffees); });

    });

    function type(d) {
      d.coffees = +d.coffees;
      return d;
    }
  </script>
<?php echo ob_get_clean() ?>
