var players_field, playtime_field, playtime_mod_field, sort_field;

$(document).ready(function() {
    players_field = $("#players");
    playtime_field = $("#playtime");
    playtime_mod_field = $("#playtime_mod");
    sort_field = [
        $("#order_alpha"),
        $("#order_playtime"),
        $("#order_score"),
        $("#order_random"),
        $("#order_desc")
    ];
    $("#new_groups").click(reload_games);
	reload_games();
});

var reload_games = function () {
    var games_list = [];

    var players = players_field[0].value;
    var playtime = playtime_field[0].value;
    var playtime_mod = playtime_mod_field[0].value;
    var order;
    switch (true) {
        case sort_field[0][0].checked: order="alpha";      break;
        case sort_field[1][0].checked: order="playtime";   break;
        case sort_field[2][0].checked: order="score";      break;
        case sort_field[3][0].checked: order="random";     break;
    }
    var desc = sort_field[4][0].checked;
    console.log(sort_field);

    var data = {};
    if (players) data.players = players;
    if (playtime) data.playtime = playtime;
    if (order) data.order = order;
    if (playtime_mod) data.playtime_mod=playtime_mod;
    if (desc) data.desc="yes";

    $.ajax({

        url: 'api/games',

        type: "GET",

        data: data,

        success: function (data) {
            games_list = data;
            $('.games').html("");
            for (var i = 0; i < data.length; i++) {
                $('.games').append("<li class=\"game\" data-index=" + i + ">" + data[i].game + " :: " + data[i].playingtime + " minutes</li>");
            }

            $(".game").click(function (ev) {
                var j = parseInt($(ev.currentTarget).data('index'));
                var game_json = games_list[j];
                alert(game_json.game + " :: " + game_json.playingtime + " minutes");
            });

        },

        error: function () {
            $('body').html("Error happened");
        }

    });
};
