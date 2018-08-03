/**
 * Created by Maxim on 05 Feb 2016.
 */

// ------------------ Jquery Events --------------------- //

var importLocked = false;

$(function () {

    if(typeof $.fn.chosen == "function")
        $("#subGwLangs, #targetLangs, "
            + "#sourceTranslation, "
            + "#sourceTranslationNotes, "
            + "#gwLang, #projectMode")
            .chosen();

    // Open gateway project form
    $("#cregwpr").click(function () {
        $("#gwProject").trigger("reset");
        $(".errors").html("");
        $(".main-content").css("left", 0);
    });

    $(".panel-close").click(function() {
        $(this).parents(".form-panel").css("left", "-9999px");
    });

    // Submit gateway project form
    $("#gwProject").submit(function(e) {

        $.ajax({
            url: $("#gwProject").prop("action"),
            method: "post",
            data: $("#gwProject").serialize(),
            dataType: "json",
            beforeSend: function() {
                $(".gwProjectLoader").show();
            }
        })
            .done(function(data) {
                if(data.success)
                {
                    $(".form-panel").css("left", "-9999px");

                    renderPopup(data.success, function () {
                        location.reload();
                    });
                }
                else
                {
                    $(".errors").html(data.error);
                }
            })
            .always(function() {
                $(".gwProjectLoader").hide();
            });

        e.preventDefault();
    });

    // Open project Form
    /*$("select[name=sourceTranslation]").change(function() {
        if($(this).val() != ""&& $(this).val() != "udb|en" && $(this).val() != "ulb|en")
        {
            $(".projectType").removeClass("hidden");
            $("#projectType").chosen();
        }
        else
        {
            $(".projectType").addClass("hidden");
        }
    });*/

    $("select[name=projectMode]").change(function() {
        if($(this).val() == "bible")
        {
            $("#sourceTranslationNotes").val('').trigger("chosen:updated");
            $(".sourceTranslationNotes").addClass("hidden");
            $("#sourceTranslationQuestions").val('').trigger("chosen:updated");
            $(".sourceTranslationQuestions").addClass("hidden");
            $("#sourceTranslationWords").val('').trigger("chosen:updated");
            $(".sourceTranslationWords").addClass("hidden");
            $(".projectType").removeClass("hidden");
            $(".sourceTranslation").removeClass("hidden");
        }
        else if($(this).val() == "tn")
        {
            $(".sourceTranslationNotes").removeClass("hidden");
            $("#sourceTranslationNotes").chosen();
            $("#sourceTranslationQuestions").val('').trigger("chosen:updated");
            $(".sourceTranslationQuestions").addClass("hidden");
            $("#sourceTranslationWords").val('').trigger("chosen:updated");
            $(".sourceTranslationWords").addClass("hidden");
            $(".projectType").removeClass("hidden");
            $(".sourceTranslation").removeClass("hidden");
        }
        else if($(this).val() == "tq")
        {
            $(".sourceTranslationQuestions").removeClass("hidden");
            $("#sourceTranslationQuestions").chosen();
            $("#sourceTranslationNotes").val('').trigger("chosen:updated");
            $(".sourceTranslationNotes").addClass("hidden");
            $("#sourceTranslationWords").val('').trigger("chosen:updated");
            $(".sourceTranslationWords").addClass("hidden");
            $(".projectType").addClass("hidden");
            $(".sourceTranslation").addClass("hidden");
        }
        else if($(this).val() == "tw")
        {
            $(".sourceTranslationWords").removeClass("hidden");
            $("#sourceTranslationWords").chosen();
            $("#sourceTranslationNotes").val('').trigger("chosen:updated");
            $(".sourceTranslationNotes").addClass("hidden");
            $("#sourceTranslationQuestions").val('').trigger("chosen:updated");
            $(".sourceTranslationQuestions").addClass("hidden");
            $(".projectType").addClass("hidden");
            $(".sourceTranslation").addClass("hidden");
        }
    });
    

    $("#crepr").click(function () {
        $("#project").trigger("reset");
        $(".subErrors").html("");
        $(".sub-content").css("left", 0);
        /*$(".projectType").addClass("hidden");*/
        $("#project select").val('').trigger("chosen:updated");
        $("#projectType").chosen();
    });

    // Get list of target languages for gateway language
    $("#subGwLangs").change(function() {
        var tlOptions = "<option value=''></option>";

        if($(this).val() == "") {
            $("#targetLangs").html(tlOptions);
            return;
        }

        $.ajax({
                url: "/admin/rpc/get_target_languages",
                method: "post",
                data: {gwLang: $("#subGwLangs").val()},
                dataType: "json",
                beforeSend: function() {
                    $(".subGwLoader").show();
                }
            })
            .done(function(data) {
                if(data.length <= 0) return false;

                if(typeof data.login != "undefined")
                    location.reload();

                $.each(data.targetLangs, function (i, v) {
                    tlOptions += '<option value="'+ v.langID+'">'+
                        '['+v.langID+'] '+v.langName+(v.angName != "" && v.langName != v.angName ? ' ( '+v.angName+' )' : '')+
                    '</option>';
                });
                $("#targetLangs").html(tlOptions);
                $("#project select").trigger("chosen:updated");
            })
            .always(function() {
                $(".subGwLoader").hide();
            });
    });

    // Submit project form
    $("#project").submit(function(e) {
        $.ajax({
                url: $("#project").prop("action"),
                method: "post",
                data: $("#project").serialize(),
                dataType: "json",
                beforeSend: function() {
                    $(".projectLoader").show();
                }
            })
            .done(function(data) {
                if(typeof data.login != "undefined")
                {
                    location.reload();
                    return false;
                }

                if(data.success)
                {
                    $(".form-panel").css("left", "-9999px");

                    renderPopup(data.success, function () {
                        location.reload();
                    });
                }
                else
                {
                    $(".subErrors").html(data.error);
                }
            })
            .always(function() {
                $(".projectLoader").hide();
            });

        e.preventDefault();
    });


    // Event options
    $("#translators").spinner({
        min: 1,
        step: 1,
        start: 1
    });

    $("#checkers_l2, #checkers_l3").spinner({
        min: 1,
        step: 1,
        start: 1
    });

    // ------------------ DateTimePicker functionality ------------------- //
    /*if(typeof $.datepicker != "undefined" && typeof $.timepicker != "undefined")
    {
        var timeFormat;
        var timezoneList = [
            { value: -720, label: '(UTC-12:00) International Date Line West'},
            { value: -660, label: '(UTC-11:00) Midway Island, Samoa' },
            { value: -600, label: '(UTC-10:00) Hawaii' },
            { value: -540, label: '(UTC-09:00) Alaska' },
            { value: -480, label: '(UTC-08:00) Pacific Time (US and Canada); Tijuana' },
            { value: -420, label: '(UTC-07:00) Mountain Time (US and Canada), Chihuahua, La Paz, Mazatlan' },
            { value: -360, label: '(UTC-06:00) Central Time (US and Canada), Guadalajara, Mexico City, Monterrey' },
            { value: -300, label: '(UTC-05:00) Eastern Time (US and Canada), Bogota, Lima, Quito' },
            { value: -240, label: '(UTC-04:00) Atlantic Time (Canada), Caracas, La Paz' },
            { value: -210, label: '(UTC-04:30) Newfoundland and Labrador' },
            { value: -180, label: '(UTC-03:00) Buenos Aires, Georgetown, Greenland' },
            { value: -120, label: '(UTC-02:00) Mid-Atlantic' },
            { value: -60, label: '(UTC-01:00) Azores, Cape Verde Islands' },
            { value: 0, label: '(UTC+00:00) Greenwich Mean Time: Dublin, Edinburgh, Lisbon, London' },
            { value: 60, label: '(UTC+01:00) Belgrade, Sarajevo, Brussels, Madrid, Paris, Berlin, Rome, West Central Africa' },
            { value: 120, label: '(UTC+02:00) Bucharest, Cairo, Helsinki, Kiev, Tallinn, Athens, Istanbul, Jerusalem' },
            { value: 180, label: '(UTC+03:00) Moscow, Volgograd, Kuwait, Nairobi, Baghdad' },
            { value: 210, label: '(UTC+03:30) Tehran' },
            { value: 240, label: '(UTC+04:00) Abu Dhabi, Baku, Tbilisi, Yerevan' },
            { value: 270, label: '(UTC+04:30) Kabul' },
            { value: 300, label: '(UTC+05:00) Ekaterinburg, Islamabad, Karachi, Tashkent' },
            { value: 330, label: '(UTC+05:30) Kolkata, Mumbai, New Delhi' },
            { value: 345, label: '(UTC+05:45) Kathmandu' },
            { value: 360, label: '(UTC+06:00) Astana, Dhaka, Almaty, Novosibirsk' },
            { value: 390, label: '(UTC+06:30) Yangon Rangoon' },
            { value: 420, label: '(UTC+07:00) Bangkok, Hanoi, Jakarta, Krasnoyarsk' },
            { value: 480, label: '(UTC+08:00) Beijing, Hong Kong SAR, Kuala Lumpur, Singapore, Irkutsk' },
            { value: 540, label: '(UTC+09:00) Seoul, Osaka, Tokyo, Yakutsk' },
            { value: 570, label: '(UTC+09:30) Darwin, Adelaide' },
            { value: 600, label: '(UTC+10:00) Canberra, Melbourne, Sydney, Brisbane, Vladivostok, Guam' },
            { value: 660, label: '(UTC+11:00) Magadan, Solomon Islands, New Caledonia' },
            { value: 720, label: '(UTC+12:00) Fiji Islands, Kamchatka, Marshall Islands, Auckland, Wellington' },
        ];
        var lang = typeof getCookie("lang") != "undefined" ? getCookie("lang") : "en";

        switch (lang)
        {
            case "ru":
                timeFormat = "HH:mm z";
                break;

            default:
                timeFormat = "hh:mm TT z";
                break;
        }

        $( "#cal_from" ).datetimepicker({
            timeFormat: timeFormat,
            timezoneList: timezoneList,
            //minDate: new Date(),
            onClose: function( selectedDate ) {
                if(selectedDate != "")
                    $( "#cal_to" ).datepicker( "option", "minDate", selectedDate );
            },
        });

        $( "#cal_to" ).datetimepicker({
            //defaultDate: "+1w",
            timeFormat: timeFormat,
            timezoneList: timezoneList,
            //minDate: new Date(),
            onClose: function( selectedDate ) {
                if(selectedDate != "")
                    $( "#cal_from" ).datepicker( "option", "maxDate", selectedDate );
            }
        });
    }*/

    if($().ajaxChosen)
    {
        $("#adminsSelect").ajaxChosen({
                type: 'post',
                url: '/admin/rpc/get_members',
                dataType: 'json',
                minTermLength: 1,
                afterTypeDelay: 500,
                jsonTermKey: "search",
                lookingForMsg: Language.searchingFor,
            },
            function (data)
            {
                var terms = {};

                $.each(data, function (i, val) {
                    terms[i] = val;
                });

                return terms;
            },
            {
                no_results_text: Language.noResultText
            });
    }

    // Open event form
    $(".startEvnt").click(function() {
        var bookCode = $(this).attr("data");
        var bookName = $(this).attr("data2");
        var chapterNum = $(this).attr("data3");
        var sourceLangID = $("#sourceLangID").val();
        var sourceBible = $("#sourceBible").val();
        var bookProject = $("#bookProject").val();

        $("button[name=startEvent]").text(Language.create);
        $("button[name=deleteEvent]").hide();
        $("button[name=progressEvent]").hide();
        $("button[name=manageEvent]").hide();
        $(".delinput").hide();
        $("#startEvent").trigger("reset");
        $("#eventAction").val("create");
        $(".errors").html("");
        $(".bookName").text(bookName);
        $("#bookCode").val(bookCode);
        $(".event-content").css("left", 0);
        $("#adminsSelect").empty().trigger("chosen:updated");

        if(["tn"].indexOf(bookProject) > -1)
        {
            $(".importTranslation").hide();
            $(".importInfo").hide();
        }
        else
        {
            $(".importTranslation").show();
            $(".importInfo").show();
        }

        $(".l2_buttons").hide();
        $(".breaks").hide();
        $("button[name=startEvent]").prop("disabled", false);

        $(".book_info_content").html(
            '(<strong>'+Language.chaptersNum+':</strong> '+chapterNum+')'
        );
    });


    // Submit event form
    $("#startEvent").submit(function(e) {

        $.ajax({
                url: $("#startEvent").prop("action"),
                method: "post",
                data: $("#startEvent").serialize(),
                dataType: "json",
                beforeSend: function() {
                    $(".startEventLoader").show();
                }
            })
            .done(function(data) {
                if(data.success)
                {
                    location.reload();
                }
                else
                {
                    $(".errors").html(data.error);
                }
            })
            .always(function() {
                $(".startEventLoader").hide();
            });

        e.preventDefault();
    });

    // Edit event form
    $(".editEvnt").click(function () {
        var bookCode = $(this).attr("data");
        var eventID = $(this).attr("data2");
        var abbrID = $(this).attr("data4");

        $("#eID").val(eventID);
        $("#startEvent").trigger("reset");
        $(".errors").html("");
        $("#eventAction").val("edit");
        $("#adminsSelect").empty();
        $("#abbrID").val(abbrID);
        $("#bookCode").val(bookCode);
        $("button[name=deleteEvent]").show();
        $("button[name=progressEvent]").show();
        $("button[name=manageEvent]").show();
        $(".delinput").hide();
        var delLabel = $(".delinput label");

        $.ajax({
            url: "/admin/rpc/get_event",
            method: "post",
            data: {eventID: eventID},
            dataType: "json",
            beforeSend: function() {
                $(".editEvnt").prop("disabled", true);
            }
        })
            .done(function(data) {
                if(data.success)
                {
                    if(EventStates.states[data.event.state] >= EventStates.states.translating
                        || ["tn"].indexOf(data.event.bookProject) > -1)
                    {
                        $(".importTranslation").hide();
                        $(".importInfo").hide();
                    }
                    else
                    {
                        $(".importTranslation").show();
                        $(".importInfo").show();
                    }

                    $("button[name=startEvent]").text(Language.save);
                    if(EventStates.states[data.event.state] >= EventStates.states.translated)
                    {
                        $(".l2_buttons").css("display", "inline-block");
                        $(".breaks").show();
                        $("button[name=startEvent]").prop("disabled", true);
                        $("button[name=deleteEvent]").prop("disabled", true);
                        //$("button[name=manageEvent]").prop("disabled", true);
                        if(EventStates.states[data.event.state] < EventStates.states.l2_recruit)
                        {
                            $("#eventAction").val("create");
                            $("button[name=startL2Event]").text(Language.create);
                            $("button[name=deleteL2Event]").hide();
                            $("button[name=progressL2Event]").hide();
                            $("button[name=manageL2Event]").hide();
                            $("span", delLabel).remove();
                        }
                        else
                        {
                            $("button[name=startL2Event]").text(Language.save);
                            $("button[name=deleteL2Event]").show();
                            $("button[name=progressL2Event]").show();
                            $("button[name=manageL2Event]").show();
                            if(delLabel.has("span").length <= 0)
                                delLabel.append(" <span>(L2)</span>");
                        }
                    }
                    else
                    {
                        $("button[name=startEvent]").prop("disabled", false);
                        $("button[name=deleteEvent]").prop("disabled", false);
                        //$("button[name=manageEvent]").prop("disabled", false);
                        $(".l2_buttons").hide();
                        $(".breaks").hide();
                        $("span", delLabel).remove();
                    }

                    $(".bookName").text(data.event.name);
                    $(".book_info_content").html(
                        '(<strong>'+Language.chaptersNum+':</strong> '+data.event.chaptersNum+')'
                    );

                    var admins = data["admins"];
                    var content = "";
                    $.each(admins, function(k, v) {
                        content += '' +
                            '<option value="' + k + '" selected>' + v + '</option>';
                    });

                    $("#adminsSelect").prepend(content);
                    $(".event-content").css("left", 0);
                }
                else
                {
                    if(typeof data.error != "undefined")
                    {
                        if(data.error == "login" || data.error == "admin")
                            window.location.href = "/members/login";
                        else
                        {
                            renderPopup(data.error);
                        }
                    }
                }
            })
            .always(function() {
                $(".editEvnt").prop("disabled", false);
                $("#adminsSelect").trigger("chosen:updated");
            });
    });


    $("button[name=deleteEvent], button[name=deleteL2Event]").click(function (e) {
        var bookName = $(".bookName").text();
        var delName = $("#delevnt").val();
        var delinput = $(".delinput");

        if(delinput.is(":visible") && bookName == delName)
        {
            $("#eventAction").val("delete");
        }
        else
        {
            delinput.show();
            e.preventDefault();
        }
    });

    $("button[name=progressEvent]").click(function (e) {
        var eventID = $("#eID").val();
        var mode = $(this).data("mode");
        var add = ["tn","sun"].indexOf(mode) > -1 ? "-"+mode : "";
        window.location = "/events/information"+add+"/"+eventID;
        e.preventDefault();
    });
    
    $("button[name=progressL2Event]").click(function (e) {
        var eventID = $("#eID").val();
        window.location = "/events/information-l2/"+eventID;
        e.preventDefault();
    });

    $("button[name=manageEvent]").click(function (e) {
        var eventID = $("#eID").val();
        window.location = "/events/manage/"+eventID;
        e.preventDefault();
    });
    
    $("button[name=manageL2Event]").click(function (e) {
        var eventID = $("#eID").val();
        window.location = "/events/manage-l2/"+eventID;
        e.preventDefault();
    });

    $("button[name=updateAllCache]").click(function (e) {
        var $this = $(this);
        var sourceLangID = $this.data("sourcelangid");
        var sourceBible = $this.data("sourcebible");

        $.ajax({
            url: "/admin/rpc/update_all_cache",
            method: "post",
            data: {
                sourceLangID: sourceLangID,
                sourceBible: sourceBible
            },
            dataType: "json",
            beforeSend: function() {
                $(".cacheLoader").show();
                $this.prop("disabled", true);
            }
        })
            .done(function(data) {
                if(data.success)
                {
                    renderPopup(Language.cacheUpdated + ": " + data.booksUpdated + " " + Language.books);
                }
                else
                    renderPopup(Language.commonError, function () {
                        window.location.reload();
                    }, function () {
                        window.location.reload();
                    });
            })
            .always(function() {
                $(".cacheLoader").hide();
                $this.prop("disabled", false);
            });

        e.preventDefault();
    });

    $("button[name=clearCache]").click(function (e) {
        var $this = $(this);
        var abbrID = $("#abbrID").val();
        var bookCode = $("#bookCode").val();
        var sourceLangID = $("#sourceLangID").val();
        var sourceBible = $("#sourceBible").val();

        $.ajax({
            url: "/admin/rpc/clear_cache",
            method: "post",
            data: {
                abbrID: abbrID,
                bookCode: bookCode,
                sourceLangID: sourceLangID,
                sourceBible: sourceBible
            },
            dataType: "json",
            beforeSend: function() {
                $(".startEventLoader").show();
                $this.prop("disabled", true);
            }
        })
            .done(function(data) {
                if(data.success)
                {
                    renderPopup(Language.cacheUpdated);
                }
                else
                    renderPopup(Language.commonError, function () {
                        window.location.reload();
                    }, function () {
                        window.location.reload();
                    });
            })
            .always(function() {
                $(".startEventLoader").hide();
                $this.prop("disabled", false);
            });
        e.preventDefault();
    });

    $("span[name=importTranslation]").click(function (e) {
        $(".event-content").css("left", -9000);
        $(".import_menu_content").css("left", 0);
    });

    $(".import_menu ul li:last-child").click(function () {
        if(importLocked) return false;
        $(".import_menu_content").css("left", -9000);
        $(".event-content").css("left", 0);
    });

    $(".dcs_import_menu ul li:last-child").click(function () {
        if(importLocked) return false;
        $(".dcs_import_menu_content").css("left", -9000);
        $(".import_menu_content").css("left", 0);
    });

    $(".import_menu label").click(function () {
        if(importLocked) return false;
        return true;
    });

    $("#dcs_form").submit(function (e) {
        e.preventDefault();
        return false;
    });

    $(".import_menu input[name=import]").change(function () {
        if(importLocked) return false;

        var input = $(this);
        var form = $(this).parents("form");
        var formdata = false;
        var eventID = $("#eID").val();
        if (window.FormData){
            formdata = new FormData(form[0]);
            formdata.append("eventID", eventID);
        }

        $.ajax({
            url         : '/admin/rpc/import',
            data        : formdata ? formdata : form.serialize(),
            cache       : false,
            contentType : false,
            processData : false,
            type        : 'POST',
            dataType    : "json",
            beforeSend: function() {
                importLocked = true;
                $(".importLoader").show();
            }
        })
            .done(function(response) {
                if(response.success)
                {
                    renderPopup(response.message, function () {
                        location.reload();
                    }, function () {
                        location.reload();
                    });
                }
                else
                {
                    renderPopup(response.error);
                }
            })
            .always(function() {
                input.val("");
                importLocked = false;
                $(".importLoader").hide();
            });
    });

    $(".import_menu ul li").click(function () {
        var type = $(this).data("type");
        if(type == "dcs")
        {
            $(".dcs_list tbody").html("");
            $("input[name=dcs_repo_name]").val("");
            $(".import_menu_content").css("left", -9000);
            $(".dcs_import_menu_content").css("left", 0);
        }
    });


    var dcs_timeout = null;
    $("body").on("keyup", "input[name=dcs_repo_name]", function () {
        if(importLocked) return false;

        var q = $(this).val();

        clearTimeout(dcs_timeout);
        dcs_timeout = setTimeout(function() {
            $.ajax({
                url: "/admin/rpc/repos_search/" + q,
                method: "get",
                dataType: "json",
                beforeSend: function() {
                    $(".importLoader").show();
                }
            })
                .done(function(response) {
                    $(".dcs_list tbody").html("");
                    if(response.data.length > 0)
                    {
                        $.each(response.data, function (i, v) {
                            var ts = Date.parse(v.updated_at);
                            var date = new Date(ts);

                            var list = "<tr data-url='"+ v.clone_url +"'>";
                            list += "<td>"+ v.owner.login +"</td>";
                            list += "<td>" + v.name +"</td>";
                            list += "<td>" + date.toLocaleString() +"</td>";
                            list += "</tr>";
                            $(".dcs_list tbody").append(list);
                        });
                    }
                    else
                    {
                        // TODO show "nothing found" message
                    }
                })
                .always(function() {
                    $(".importLoader").hide();
                });
        }, 1000);
    });

    $("body").on("click", ".dcs_list tbody tr", function() {
        if(importLocked) return false;

        var repo_url = $(this).data("url");
        var eventID = $("#eID").val();

        $.ajax({
            url: "/admin/rpc/import",
            method: "post",
            data: {
                import: repo_url,
                type: "dcs",
                eventID: eventID
            },
            dataType: "json",
            beforeSend: function() {
                importLocked = true;
                $(".importLoader").show();
            }
        })
            .done(function(response) {
                if(response.success)
                {
                    renderPopup(response.message, function () {
                        location.reload();
                    }, function () {
                        location.reload();
                    });
                }
                else
                {
                    renderPopup(response.error);
                }
            })
            .always(function() {
                importLocked = false;
                $(".importLoader").hide();
            });
    });
    

    // Show event contributors
    $(".showContributors").click(function () {
        var eventID = $(this).data("eventid");
        var level = $(this).data("level");

        $.ajax({
            url: "/admin/rpc/get_event_contributors",
            method: "post",
            data: {eventID: eventID, level: level},
            dataType: "json",
            beforeSend: function() {
                $(".showContributors").prop("disabled", true);
            }
        })
            .done(function(data) {
                if(data.success)
                {
                    var html = "";

                    // Render facilitators
                    html += "<div class='admins_list'>" +
                        "<div class='contrib_title'>"+Language.facilitators+":</div>";
                    $.each(data.admins, function (i,v) {
                        html += "<div>" +
                            "<a href='/members/profile/"+i+"'>"+v.userName+" ("+v.name+")</a>" +
                            "</div>";
                    });
                    html += "</div>";

                    // Render translators
                    if(Object.keys(data.translators).length > 0)
                    {
                        html += "<div class='translators_list'>" +
                            "<div class='contrib_title'>"+Language.translators+":</div>";
                        $.each(data.translators, function (i,v) {
                            html += "<div>" +
                                "<a href='/members/profile/"+i+"'>"+v.userName+" ("+v.name+")</a>" +
                                "</div>";
                        });
                        html += "</div>";
                    }

                    // Render checkers
                    html += "<div class='checkers_list'>" +
                        "<div class='contrib_title'>"+Language.checkers+":</div>";
                    $.each(data.checkers, function (i,v) {
                        html += "<div>" +
                            "<a href='/members/profile/"+i+"'>"+v.userName+" ("+v.name+")</a>" +
                            "</div>";
                    });
                    html += "</div>";

                    $(".contributors_content").html(html);
                    $(".contributors_container").css("left", 0);
                }
                else
                {
                    if(typeof data.error != "undefined")
                    {
                        if(data.error == "login" || data.error == "admin")
                            window.location.href = "/members/login";
                        else
                        {
                            renderPopup(data.error);
                        }
                    }
                }
            })
            .always(function() {
                $(".showContributors").prop("disabled", false);
            });
    });

    $(".contributors-close").click(function () {
        $(".contributors_container").css("left", -9999);
    });


    // Activate/Verify member
    $(".verifyMember").click(function (e) {
        e.preventDefault();

        var memberID = $(this).attr("data");
        var parent = $(this).parents("tr");
        var activated = $(".activateMember", parent).is(":checked");

        var msg = "";
        if(!activated)
            msg += Language.notActivatedWarning + " ";
        msg += Language.verifyMessage;

        renderConfirmPopup(Language.verifyTitle, msg, function () {
            $(this).dialog("close");

            $.ajax({
                url: "/admin/rpc/verify_member",
                method: "post",
                data: {
                    memberID: memberID,
                },
                dataType: "json",
                beforeSend: function() {
                    //$(".commentEditorLoader").show();
                }
            })
                .done(function(data) {
                    if(data.success)
                    {
                        // renderPopup(Language.verifySuccess);
                        parent.remove();
                    }
                    else
                    {
                        if(typeof data.error != "undefined")
                        {
                            renderPopup(data.error, function () {
                                window.location.reload(true);
                            });
                        }
                    }
                })
                .always(function() {
                    //$(".commentEditorLoader").hide();
                });
        });

        return false;
    });


    // Block/Unblock member
    $(document).on("click", ".blockMember", function (e) {
        e.preventDefault();

        var $this = $(this);
        var memberID = $this.attr("data");

        $.ajax({
            url: "/admin/rpc/block_member",
            method: "post",
            data: {
                memberID: memberID,
            },
            dataType: "json",
            beforeSend: function() {
                //$(".commentEditorLoader").show();
            }
        })
            .done(function(data) {
                if(data.success)
                {
                    if(data.blocked)
                    {
                        renderPopup(Language.blockedSuccess);
                        $this.removeClass("btn-danger")
                            .addClass("btn-primary")
                            .text(Language.unblock);
                    }
                    else
                    {
                        renderPopup(Language.unblockedSuccess);
                        $this.removeClass("btn-primary")
                            .addClass("btn-danger")
                            .text(Language.block);
                    }
                }
                else
                {
                    if(typeof data.error != "undefined")
                    {
                        renderPopup(data.error, function () {
                            window.location.reload(true);
                        });
                    }
                }
            })
            .always(function() {
                //$(".commentEditorLoader").hide();
            });
    });

    // Members tabs switch
    $(".mems_tab").click(function () {
        var id = $(this).attr("id");

        $(".members_content").removeClass("shown");
        $(".mems_tab").removeClass("active");

        $(this).addClass("active");
        $("#"+id+"_content").addClass("shown");

        if(id == "all_members")
            $("select.mems_language").chosen();

        return false;
    });

    // Submit Filter form
    $(".filter_apply button").click(function () {
        var button = $(this);
        button.prop("disabled", true);
        $(".filter_page").val(1);

        $.ajax({
            url: "/admin/rpc/search_members",
            method: "post",
            data: $("#membersFilter").serialize(),
            dataType: "json",
            beforeSend: function() {
                $(".filter_loader").show();
            }
        })
            .done(function(data) {
                if(data.success)
                {
                    if(data.members.length > 0)
                    {
                        $("#all_members_table").show();
                        $(".filter_page").val(1);

                        // if it has more results to show draw "more" button
                        if(data.members.length < parseInt(data.count))
                        {
                            if($("#search_more").length <= 0)
                            {
                                $('<div id="search_more"></div>').appendTo("#all_members_content")
                                    .text(Language.searchMore);
                            }
                            $(".filter_page").val(2);
                        }
                        else
                        {
                            $("#search_more").remove();
                        }

                        $("#search_empty").remove();
                    }
                    else
                    {
                        $("#all_members_table").hide();
                        if($("#search_empty").length <= 0)
                            $('<div id="search_empty"></div>').appendTo("#all_members_content")
                                .text(Language.searchEmpty);
                        $('#search_more').remove();
                    }

                    $("#all_members_table tbody").html("");
                    $.each(data.members, function (i, v) {
                        var row = "<tr>" +
                            "<td><a href='/members/profile/"+v.memberID+"'>"+v.userName+"</a></td>" +
                            "<td>"+v.firstName+" "+v.lastName+"</td>" +
                            "<td>"+v.email+"</td>" +
                            "<td>"+(v.prefered_roles != "" && v.prefered_roles != null
                                ? JSON.parse(v.prefered_roles).map(function(role) {
                                        return " "+Language[role];
                                    })
                                : "<span style='color: #f00'>"+Language.emptyProfileError)+"</span></td>" +
                            "<td><input type='checkbox' "+(parseInt(v.isAdmin) ? "checked" : "")+" disabled></td>" +
                            "<td><button class='blockMember btn "+(v.blocked == 1 ? "btn-primary" : "btn-danger")+"' data='"+v.memberID+"'>" +
                                (v.blocked == 1 ? Language.unblock : Language.block)+"</button></td>" +
                            "</tr>";
                        $("#all_members_table tbody").append(row);
                    });
                }
                else
                {
                    if(typeof data.error != "undefined")
                    {
                        renderPopup(data.error);
                    }
                }
            })
            .always(function() {
                $(".filter_loader").hide();
                button.prop("disabled", false);
            });

        return false;
    });

    $(document).on("click", "#search_more", function () {
        var button = $(this);

        if(button.hasClass("disabled")) return false;

        button.addClass("disabled");
        var page = parseInt($(".filter_page").val());

        $.ajax({
            url: "/admin/rpc/search_members",
            method: "post",
            data: $("#membersFilter").serialize(),
            dataType: "json",
            beforeSend: function() {
                $(".filter_loader").show();
            }
        })
            .done(function(data) {
                if(data.success)
                {
                    if(data.members.length > 0)
                    {
                        $(".filter_page").val(page+1);
                        $.each(data.members, function (i, v) {
                            var row = "<tr>" +
                                "<td><a href='/members/profile/"+v.memberID+"'>"+v.userName+"</a></td>" +
                                "<td>"+v.firstName+" "+v.lastName+"</td>" +
                                "<td>"+v.email+"</td>" +
                                "<td>"+(v.prefered_roles != "" && v.prefered_roles != null
                                    ? JSON.parse(v.prefered_roles).map(function(role) {
                                    return " "+Language[role];
                                })
                                    : "<span style='color: #f00'>"+Language.emptyProfileError)+"</span></td>" +
                                "<td><input type='checkbox' "+(parseInt(v.isAdmin) ? "checked" : "")+" disabled></td>" +
                                "<td><button class='blockMember btn "+(v.blocked == 1 ? "btn-primary" : "btn-danger")+"' data='"+v.memberID+"'>" +
                                    (v.blocked == 1 ? Language.unblock : Language.block)+"</button></td>" +
                                "</tr>";
                            $("#all_members_table tbody").append(row);
                        });

                        var results = parseInt($("#all_members_table tbody tr").length);
                        if(results >= parseInt(data.count))
                            $('#search_more').remove();
                    }
                    else
                    {
                        $('#search_more').remove();
                    }
                }
                else
                {
                    if(typeof data.error != "undefined")
                    {
                        renderPopup(data.error);
                    }
                }
            })
            .always(function() {
                $(".filter_loader").hide();
                button.removeClass("disabled");
            });
    });

    // Clear members filter
    $(".filter_clear").click(function () {
        $("#membersFilter")[0].reset();
        $(".mems_language").val('').trigger("chosen:updated");
        return false;
    });

    // Admin tools

    // Update languages database
    $(".update_langs button").click(function () {
        $.ajax({
            url: "/admin/rpc/update_languages",
            method: "post",
            dataType: "json",
            beforeSend: function() {
                $(".update_langs img").show();
            }
        })
            .done(function(data) {
                if(data.success)
                {
                    renderPopup("Updated!");
                }
                else
                {
                    if(typeof data.error != "undefined")
                    {
                        renderPopup(data.error);
                    }
                }
            })
            .always(function() {
                $(".update_langs img").hide();
            });
    });

    // Create multiple users
    $(".create_users button").click(function () {
        var amount = $(".create_users #amount").val();
        var langs = $(".create_users #langs").val();
        var password = $(".create_users #password").val();

        $.ajax({
            url: "/admin/rpc/create_multiple_users",
            method: "post",
            data: {amount: amount, langs: langs, password: password},
            dataType: "json",
            beforeSend: function() {
                $(".create_users img").show();
            }
        })
            .done(function(data) {
                if(data.success)
                {
                    $(".create_users #amount").val("");
                    $(".create_users #langs").val("");
                    $(".create_users #password").val("");

                    renderPopup(data.msg);
                }
                else
                {
                    if(typeof data.error != "undefined")
                    {
                        renderPopup(data.error);
                    }
                }
            })
            .always(function() {
                $(".create_users img").hide();
            });
    });


    // Managing SAIL Dictionary

    // Delete word
    $("body").on("click", ".tools_delete_word", function (e) {
        var li = $(this).parent("li");
        var word = li.attr("id");

        renderConfirmPopup(Language.attention, Language.delSailword + word + "?", function () {
            $( this ).dialog( "close" );
            $.ajax({
                url: "/admin/rpc/delete_sail_word",
                method: "post",
                data: {word: word},
                dataType: "json",
                beforeSend: function() {
                    $("img", li).show();
                }
            })
                .done(function(data) {
                    if(data.success)
                    {
                        li.remove();
                    }
                    else
                    {
                        if(typeof data.error != "undefined")
                        {
                            renderPopup(data.error);
                        }
                    }
                })
                .always(function() {
                    $("img", li).hide();
                });
        });

        e.preventDefault();
        return false;
    });

    // Create word
    $("body").on("click", ".sail_create .add_word", function (e) {
        var word = $("#sailword").val();
        var symbol = $("#sailsymbol").val();

        $.ajax({
            url: "/admin/rpc/create_sail_word",
            method: "post",
            data: {word: word, symbol: symbol},
            dataType: "json",
            beforeSend: function() {
                $("#sail_create_loader").show();
            }
        })
            .done(function(data) {
                if(data.success)
                {
                    $("#sailword").val("");
                    $("#sailsymbol").val("");
                    $(".sail_list.tools ul").append(data.li);
                }
                else
                {
                    if(typeof data.error != "undefined")
                    {
                        renderPopup(data.error);
                    }
                }
            })
            .always(function() {
                $("#sail_create_loader").hide();
            });

        e.preventDefault();
        return false;
    });


    // Create News
    $(".create_news button").click(function (e) {
        var title = $("#title").val();
        var category = $("#category").val();
        var text = $("#text").val();

        $.ajax({
            url: "/admin/rpc/create_news",
            method: "post",
            data: {title: title, category: category, text: text},
            dataType: "json",
            beforeSend: function() {
                $(".create_news img").show();
            }
        })
            .done(function(data) {
                if(data.success)
                {
                    $("#title").val("");
                    $("#category").val("");
                    $("#text").val("");

                    renderPopup(data.msg);
                }
                else
                {
                    if(typeof data.error != "undefined")
                    {
                        renderPopup(data.error);
                    }
                }
            })
            .always(function() {
                $(".create_news img").hide();
            });

        e.preventDefault();
        return false;
    });
});


// --------------- Variables ---------------- //
var EventStates = {
    states: {
        "started": 0,
        "translating": 1,
        "translated": 2,
        "l2_recruit": 3,
        "l2_check": 4,
        "l2_checked": 5,
        "l3_recruit": 6,
        "l3_check": 7,
        "complete": 8
    }
};


// --------------- Functions ---------------- //
