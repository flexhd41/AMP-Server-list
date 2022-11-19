<?php
require("incl/database.php")
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<style>
body {
  font-family: Verdana, sans-serif;
}

.title {
	text-align: center;
	font-size: 30px;
	margin: 0px;
	font-weight: bold;
}

.modstatus {
	float: right;
	position: absolute;
	top: 10px;
	right: 10px;
	text-transform: uppercase;
	font-weight: bold;
}
.modstatus p {
	margin: 0px;
}
.modstatus .btn {
	float: right;
	visibility: hidden;
}
.modstatus.error .btn {
    visibility: visible;
}
.error {
	color: red;
}
.success {
	color: green;
}

table.serverlist{
	width: 100%;
	border-collapse: collapse;
	margin-top:20px;
	font-size: 20px;
}
table.serverlist th, table.serverlist td{
	border: 1px solid #cdcdcd;
	padding: 7px;
}
table.serverlist th {
	text-transform: uppercase;
}
table.serverlist .search {
	width: 100%;
	font-size: 20px;
}
table.serverlist td:nth-child(3),
table.serverlist td:nth-child(4),
table.serverlist td:nth-child(5),
table.serverlist td:nth-child(6),
table.serverlist td:nth-child(7) {
	text-align: center;
}

table.serverlist .description {
	display: flex;
	color: #2e2e2e;
	font-size: 15px;
}

span.tick {
	float: left;
    background-color: #0ab5ec;
    color: white;
    border-radius: 50%;
    margin-right: 10px;
    font-weight: bold;
    display: inline-block;
    width: 30px;
    height: 30px;
    text-align: center;
    vertical-align: middle;
	cursor: default;
}

.btn {
	border: none;
	color: white;
	padding: 14px 28px;
	cursor: pointer;
}
.btn.green {
	background-color: #04AA6D;
}
.btn.green:hover {
	background-color: #46a049;
}
.btn.blue {
	background-color: #0499aa;
}
.btn.blue:hover {
	background-color: #008291;
}
.btn.red {
	background-color: #ff0404;
}
.btn.red:hover {
	background-color: #a60202;
}

.panel {
	background-color: white;
	border: 1px solid lightgray;
	min-height: 360px;
	min-width: 540px;
	height: 60%;
	width: 70%;
	position: fixed;
	top: 0px; right: 0px; bottom: 0px; left: 0px;
	margin: auto;
}
.panel .close {
	float: right;
	position: relative;
	top: 3px;
	right: 3px;
	font-size: 30px;
	cursor: pointer;
	line-height: 0.8;
}
.panel .close:hover {
	color: red;
}

.panel .data {
	font-size: 25px;
	position: fixed;
	top: 0px; right: 0px; bottom: 0px; left: 0px;
	margin: auto;
	border-spacing: 8px;
}

.panel .data tr td:first-child {
	text-align: right;
}
</style>
</head>
<body>
	<p class="title">Adammantium Multiplayer</p>
	<p class="title">Serverlist</p>

	<div class="modstatus">
		<p class="message"></p>
		<button onclick='connectToMod()' class='btn red'>Retry</button>
	</div>

	<table class="serverlist" id="serverlist">
		<thead>
			<tr>
				<th style="width: 64px;"></th>
				<th>Server name<br><input type="text" class="search" id="search_name" /></th>
				<th>Info</th>
				<th>Map<br><input type="text" class="search" id="search_map" /></th>
				<th>Players</th>
				<th>Address<br><input type="text" class="search" id="search_ip" /></th>
				<th style="width: 90px;">actions</th>
			</tr>
		</thead>
		<tbody>
			<?php
			function decode_boolean($str) {
				if($str) {
					return "<span style='color: green; font-weight: bold;'>&#9745;</span>";
				}else{
					return "<span style='color: red; font-weight: bold;'>&#9746;</span>";
				}
			}
			
			$result = executeQuery("select * from serverlist order by official desc, servername");
			foreach($result as $row) {
				echo "<tr>";
				
				echo "<td><img src='data:image/png;base64,".htmlspecialchars_decode($row["servericon"])."'></img></td>";
				
				$servername = htmlspecialchars_decode($row["servername"]);
				if($row["official"]) {
					$servername .= "<span title='This is a offical server' class='tick'>✓</span>";
				}
				$servername .= "<span class='description'>".htmlspecialchars_decode($row["description"])."</span>";
				
				echo "<td>".$servername."</td>";
				
				echo "<td>
						v".htmlspecialchars_decode($row["version"])."<br>
						PvP: ".decode_boolean($row["pvp"])."<br>
						Static Map: ".decode_boolean($row["static_map"])."<br>
					 </td>";
				
				echo "<td>{$row["modus"]} @ {$row["map"]}</td>";
				//echo "<td>{$row["players_connected"]} / {$row["players_max"]}</td>";
				echo "<td>? / {$row["players_max"]}</td>";
				echo "<td>".htmlspecialchars_decode($row["address"]).":{$row["port"]}</td>";
				echo "<td>
						<details style='display: none;'>
							<description>".htmlspecialchars_decode($row["description"])."</description>
						</details>
						<!--<button onclick='infoRow(this)' class='btn blue'>Info</button>--!>
						<button onclick='joinRow(this)' class='btn green'>Join</button>
					  </td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>

	<p>Big thanks to <b>flex hd</b>!</p>
	
	<div class="panel" id="details" style="display: none;">
		<div class="close">&#9746;</div>
		
		<center><h1>Join server</h1></center>
		
		<table class="data">
			<tr>
				<td>Servername:</td>
				<td id="join_name"></td>
			</tr>
			<tr>
				<td>Address:</td>
				<td id="join_address"></td>
			</tr>
			<tr>
				<td>Port:</td>
				<td id="join_port"></td>
			</tr>
			<tr>
				<td colspan=2 style="text-align: center;">
					<button id="join-invite" class='btn green'>Join Server</button>
				</td>
			</tr>
		</table>
	</div>
 
	<script>
		$("#search_name").keyup(search);
		$("#search_map").keyup(search);
		$("#search_ip").keyup(search);

		function search() {
			filter_name = $("#search_name").val().toLowerCase();
			filter_map  = $("#search_map").val().toLowerCase();
			filter_ip	= $("#search_ip").val().toLowerCase();
			
			$("#serverlist tr").filter(function() {
				var columns = $(this).find("td");
				if(columns.length == 0) return;
				
				$(this).toggle(	(filter_name.length > 0 && columns[1].innerText.toLowerCase().indexOf(filter_name) > -1)
							  ||(filter_map.length  > 0 && columns[3].innerText.toLowerCase().indexOf(filter_map)  > -1)
							  ||(filter_ip.length   > 0 && columns[5].innerText.toLowerCase().indexOf(filter_ip)   > -1)
							  ||(filter_name.length == 0 && filter_ip.length == 0 && filter_map.length == 0)
							  );
			});
	   }
	   
	   function joinRow(row) {
		   var address = $(row).closest("tr").find("td")[5].innerText;
		   
		   var ip = address.split(':')[0];
		   var port = parseInt(address.split(':')[1]);
		   
		   doJoin(ip, port);
	   }
	</script>

	<script>
	var error = false;
	const wsUri = "ws://127.0.0.1:13698/";
	var websocket;

	function connectToMod() {
		$(".modstatus .message").text("Connecting...");
		$(".modstatus").removeClass("success");
		$(".modstatus").removeClass("error");
		
		error = false;
		websocket = new WebSocket(wsUri);
		
		websocket.onopen = (e) => {
			console.log("CONNECTED");
			$(".modstatus .message").text("MOD RUNNING");
			$(".modstatus").removeClass("error");
			$(".modstatus").addClass("success");
		};

		websocket.onclose = (e) => {
			console.log("DISCONNECTED");
			if(error) {
				$(".modstatus .message").text("MOD NOT RUNNING");
				$(".modstatus").removeClass("success");
				$(".modstatus").addClass("error");
			}else{
				$(".modstatus .message").text("DISCONNECTED");
				$(".modstatus").removeClass("success");
				$(".modstatus").addClass("error");
			}
		};

		websocket.onmessage = (e) => {
			console.log("RESPONSE: " + e.data);
		};

		websocket.onerror = (e) => {
			console.log("ERROR: " + e.data);
			error = true;
		};
	}

	function doSend(message) {
		console.log("SENT: " + message);
		websocket.send(message);
	}

	function doJoin(ip, port) {
		doSend("join:" + ip + ":" + port);
	}

	connectToMod();
	</script>
	
	<script>
	if(findGetParameter("ip")) {
		$("#details").show();
		
		var ip = findGetParameter("ip");
		var port = findGetParameter("port");
		
		$("#join_address").text(ip);
		$("#join_port").text(port);
		$("#join_name").text(findGetParameter("name"));
		
		$("#join-invite").on("click", function() {
			doJoin(ip, port);
		});
	}
	
	function findGetParameter(parameterName) {
		var result = null,
			tmp = [];
		location.search
			.substr(1)
			.split("&")
			.forEach(function (item) {
			  tmp = item.split("=");
			  if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
			});
		return result;
	}
	</script>
</body>
</html>