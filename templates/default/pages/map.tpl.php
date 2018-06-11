<!DOCTYPE html>
<script src="https://cdn.apple-mapkit.com/mk/5.x.x/mapkit.js"></script>

<style>
#map {
    margin: 0 auto 1em auto;
    height: 600px; 
}
#map-nav {
    background: white;
    opacity: 0.85;
    overflow: hidden;
    text-align: center;
}
#map-nav h1 {
    margin: 0.5em 0 0.5em 0;
}
.left { 
    float: left;
    font-size: 3em;
    margin: 0.5em 0 0.5em 1em;
}
.right { 
    float: right;
    font-size: 3em;
    margin: 0.5em 1em 0.5em 0;
}
.point {
    width: 250px;
    padding: 7px 0 0 0;
    background: rgba(247, 247, 247, 0.75);
    border-radius: 5px;
    box-shadow: 10px 10px 50px rgba(0, 0, 0, 0.29);
    font-family: Helvetica, Arial, sans-serif !important;
    -webkit-transform-origin: 0 10px;
    transform-origin: 0 10px;
}

.point h1 {
    margin-top: 0;
    padding: 5px 15px;
    background: #2aaef5;
    color: rgba(255, 255, 255, 0.9);
    font-size: 16px !important;
    font-family: Helvetica, Arial, sans-serif !important;
    font-weight: 300;
}

.point section {
    padding: 0 15px 5px;
    font-size: 14px;
}

.point section p {
    margin: 5px 0;
}

.point:after {
    content: "";
    position: absolute;
    top: 7px;
    left: -13px;
    width: 0;
    height: 0;
    margin-bottom: -13px;
    border-right: 13px solid #2aaef5;
    border-top: 13px solid rgba(0, 0, 0, 0);
    border-bottom: 13px solid rgba(0, 0, 0, 0);
}

@-webkit-keyframes scale-and-fadein {
    0% {
        -webkit-transform: scale(0.2);
        opacity: 0;
    }

    100% {
        -webkit-transform: scale(1);
        opacity: 1;
    }
}

@keyframes scale-and-fadein {
    0% {
        transform: scale(0.2);
        opacity: 0;
    }

    100% {
        transform: scale(1);
        opacity: 1;
    }
}
</style>

<div class="row page-map">
    <div class="col-md-10 col-md-offset-1">
        <div>
            <div class="row">
                
                <div id="map-nav">
                    <a class="left" href="/map?date=<?= date('Y-m-d', strtotime($date . ' -1 day')) ?>"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i></a>
                    <a class="right" href="/map?date=<?= date('Y-m-d', strtotime($date . ' +1 day')) ?>"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i></a>
                    <h1><i class="fa fa-calendar"></i><?= date('F j, Y', strtotime($date)) ?></h1>
                </div>
                <div id="map"></div>             
                <script>
                mapkit.init({
                    authorizationCallback: function(done) {
                        done('eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiIsImtpZCI6IjdSUEc2NFdDMjUifQ.eyJvcmlnaW4iOiJodHRwczovL2NsZXZlcmRldmlsLmlvIiwiaXNzIjoiN1Y2VzUyNTY2MyIsImlhdCI6MTUyODM0MjIwMiwiZXhwIjoxODQzNzAyMjAyfQ.Co3C5lFlKcV5BRr5fIi4k1KFyQ9qQx6WWas4e6k2UmJgjyZE56PrG4NHdRFgh2JuGhf39N06DVVYqaO93MKN3g');
                    }
                });
                var map = new mapkit.Map("map");
                var coordinates = [];
                var annotations = [];

                function calloutForPointAnnotation(annotation) {
                    var div = document.createElement("div");
                    div.className = "point";

                    var title = div.appendChild(document.createElement("h1"));
                    title.textContent = new Date(annotation.point.timestamp).toLocaleString();
                    
                    var section = div.appendChild(document.createElement("section"));

                    var battery = section.appendChild(document.createElement("p"));
                    battery.className = "battery";
                    battery.textContent = '🔋 ' + (annotation.point.battery_level * 100).toFixed(2) + '%, ' + annotation.point.battery_state;
                    
                    var motion = section.appendChild(document.createElement("p"));
                    motion.className = "motion";
                    if (annotation.point.motion.search('walking') != -1) {
                        motion.textContent = '🚶🏻‍♂️ ' + annotation.point.speed + ' mph';
                    } else if (annotation.point.motion.search('running') != -1) {
                        motion.textContent = '🏃🏻‍♂️ ' + annotation.point.speed + ' mph';
                    } else if (annotation.point.motion.search('driving') != -1) {
                        motion.textContent = '🚗 ' + annotation.point.speed + ' mph';
                    } else if (annotation.point.motion.search('cycling') != -1) {
                        motion.textContent = '🚴🏻‍♂️ ' + annotation.point.speed + ' mph';
                    } else { 
                        motion.textContent = '🛋';
                    }

                    var altitude = section.appendChild(document.createElement("p"));
                    altitude.className = "altitude";
                    altitude.textContent = '⛰ ' + annotation.point.altitude + ' meters';

                    var connection = section.appendChild(document.createElement("p"));
                    connection.className = "connection";
                    if (annotation.point.wifi) {
                        connection.textContent = '📶 (Wi-Fi: ' + annotation.point.wifi + ')';
                    } else {
                        connection.textContent = '📶 (Cellular)';
                    }

                    return div;
                }
                var CALLOUT_OFFSET = new DOMPoint(-148, -150);
                var pointAnnotationCallout = {
                    calloutElementForAnnotation: function(annotation) {
                        return calloutForPointAnnotation(annotation);
                    },
                    calloutAnchorOffsetForAnnotation: function(annotation, element) {
                        return CALLOUT_OFFSET;
                    },
                    calloutAppearanceAnimationForAnnotation: function(annotation) {
                        return "scale-and-fadein .4s 0 1 normal cubic-bezier(0.4, 0, 0, 1.5)";
                    }
                };

                var points = <?= $points ?>; 
                for (var i = 0; i < points.length; i++) {
                    var point = points[i];
                    var coordinate = new mapkit.Coordinate(
                        point['y'], 
                        point['x']
                    );
                    coordinates.push(coordinate);
                    
                    if ((i % 50) == 0) {
                        var annotation = new mapkit.MarkerAnnotation(coordinate, {
                            callout: pointAnnotationCallout
                        });
                        annotation.point = point;
                        annotations.push(annotation);
                    }
                }
                
                var style = new mapkit.Style({
                    lineWidth: 2,
                    lineJoin: "round",
                    lineDash: [8, 4],
                    strokeColor: "#F0F"
                });
                var polyline = new mapkit.PolylineOverlay(coordinates, { style: style });

                map.showItems(annotations);
                map.addOverlay(polyline);
                </script>
            </div>
        </div>
    </div>
</div>
