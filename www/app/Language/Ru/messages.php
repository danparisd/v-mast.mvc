<?php

return array (
    
    // -------------- Main Controller ------------------ //

    // Index method
    "home" => "Главная",
    "welcome_text" => "Добро пожаловать на V-MAST!",
    "welcome_hint" => "Чтобы начать перевод, нажмите {link}",
    "welcome_message" => "Если вас пригласили присоединиться к событию перевода, нажмите {link}",
    "maintenance_work" => "Ведутся технические работы!",

    "translations_title" => "Переводы",
    "events_title" => "Книги",
    "contact_us_title" => "Обратная связь",
    "about_title" => "О нас",
    "bible" => "Библия",
    "members" => "Пользователи",
    "on" => "Вкл.",
    "off" => "Выкл.",
    "submit_crash_report" => "Отправить отчет о сбое",
    "submit" => "Отправить",

    // ------------ Members Controller ------------------ //

    // Index method
    "members_title" => "Пользователи",
    "new_members_title" => "Новые пользователи",
    "firstName" => "Имя",
    "lastName" => "Фамилия",
    "password" => "Пароль",
    "confirm_password" => "Подтверждение пароля",
    "enter_new_password" => "Введите новый пароль",
    "userName" => "Имя пользователя",
    "userNameOrEmail" => "Имя пользователя или Email",
    "email" => "Адрес электронной почты",
    "name" => "Имя",
    "accept_btn" => "Принять",
    "deny_btn" => "Отмена",
    "tou" => "Условия использования",
    "sof" => "Утверждение веры",
    "welcome_title" => "Добро пожаловать",
    "translator" => "Переводчик",
    "checker" => "Проверяющий",
    "facilitator" => "Фасилитатор",
    "facilitators" => "Фасилитаторы",
    "l2_checker" => "Проверяющий 2-го уровня",
    "l3_checker" => "Проверяющий 3-го уровня",
    "captcha_wrong" => "Капча решена не верно",
    "success" => "Успешно",
    "admin" => "Админка",
    "contact" => "Контакт",
    "add_lang" => "Добавить язык",
    "activated" => "Активирован",
    "verify" => "Принять",
    "send_message_to" => "Отправить сообщение",
    "message_subject" => "Тема",
    "message_content" => "Сообщение",
    "send" => "Отправить",
    "facilitator_message_tip" => "Это сообщение было отправлено с сайта V-MAST.com",
    "member_wrote" => "написал",
    "project_mode" => "Режим проекта",
    "choose_project_mode" => "Выберите режим проекта",
    "scripture" => "Писание",
    "bible_mode" => "Библия",
    "notes_mode" => "Заметки",
    "questions_mode" => "Вопросы",
    "words_mode" => "Слова",
    "odb_mode" => "Наш ежедневный хлеб",
    "radio_mode" => "RADIO",
    "obs_mode" => "OBS",
    "project_exists" => "Проект существует",
    "personal" => "Личные",
    "entry" => "Запись",
    "title" => "Название",
    "author" => "Автор",
    "passage" => "Послание",
    "bible_in_a_year" => "Послание года",
    "verse" => "Стих",
    "thought" => "Мысль",
    "content" => "Параграф {number}",
    "go_sun_bible" => "SUN Библия",

    // Activate method
    "activate_account_title" => "Активация учетной записи",
    "resend_activation_code" => "Не получили сообщение? Отправить еще раз.",
    "wrong_activation_email" => "Неверный электронный адрес для активации. Учетная запись с этим электронным адресом не существует либо уже активирована",
    "new_account_title" => "Новый пользователь V-Mast",
    "new_account_message" => "<strong>Полное имя:</strong> {name}, <strong>Имя пользователя:</strong> {username}",
    "members_area" => "Перейти к управлению списком пользователей",

    // Login method
    "wrong_credentials_error" => "Неверный электронный адрес/имя пользователя или пароль.",
    "not_activated_email" => "Учетная запись не активирована. <a href='/members/activate/resend/{email}'>Отправить инструкции по активации</a> снова.",
    "login" => "Войти",
    "signup" => "Регистрация",
    "logout" => "Выйти",
    "login_message" => "Регистрация",
    "already_member" => "Уже являетесь пользователем?",
    "dont_have_account" => "Нет учетной записи?",
    "login_title" => "Войти",
    "forgot_password" => "Забыли пароль?",
    "profile_message" => "Профиль",
    "member_profile_message" => "Профиль прользователя",
    "translator_activities" => "Переводческая деятельность",
    "checking_activities" => "Проверочная деятельность",
    "facilitator_activities" => "Фасилитационная деятельность",
    "common_skills" => "Общие",
    "facilitator_skills" => "Фасилитатор",
    "checker_skills" => "Образование",
    "avatar" => "Выберите аватарку",
    "male" => "Мужская",
    "female" => "Женская",
    "delete" => "Удалить",
    "weak" => "слабый",
    "moderate" => "средний",
    "strong" => "хороший",
    "expert" => "профессиональный",
    "fluent" => "отличный",
    "native" => "родной язык",
    "limited" => "начальный",
    "none" => "никакой",
    "less_than" => "менее {years}",
    "rarely" => "редко",
    "some" => "иногда",
    "much" => "часто",
    "frequently" => "очень часто",
    "other" => "Другая",
    "yes" => "Да",
    "no" => "Нет",
    "ba_edu" => "Бакалавр",
    "ma_edu" => "Магистр",
    "phd_edu" => "Кандидат/Доктор",
    "theology" => "Теология",
    "pastoral_ministry" => "Пасторское служение",
    "bible_translation" => "Перевод Библии",
    "exegetics" => "Экзегетика",
    "elder" => "Пресвитер",
    "bishop" => "Епископ",
    "pastor" => "Пастор",
    "teacher" => "Учитель",
    "denominational_leader" => "Руководитель деноминации",
    "seminary_professor" => "Преподаватель семинарии",
    "known_languages" => "Добавьте языки, которыми вы владеете",
    "known_languages_public" => "Языки",
    "select_language" => "Выберите язык",
    "language_fluency" => "Оцените свой уровень владения языком",
    "language_fluency_public" => "Уровень владения",
    "lang_geographic_years" => "Сколько времени из последних 10 лет вашей жизни вы прожили на территории, где распространен данный язык?",
    "lang_geographic_years_public" => "Прожито на территории языка",
    "years" => "{years} лет",
    "prefered_roles" => "Предпочитаемые роли",
    "bbl_trans_yrs" => "Сколько лет вы занимаетесь переводом Библии?",
    "bbl_trans_yrs_public" => "Сколько лет занимается переводом Библии",
    "othr_trans_yrs" => "Сколько лет вы занимаетесь другими видами перевода?",
    "othr_trans_yrs_public" => "Сколько лет занимается другими видами перевода",
    "bbl_knwlg_degr" => "Оцените свой уровень знания Библии",
    "bbl_knwlg_degr_public" => "Уровень знания Библии",
    "mast_evnts" => "В каком количестве мероприятий MAST вы принимали участие",
    "mast_evnts_public" => "В каком количестве мероприятий MAST принимал(а) участие",
    "mast_role" => "Какую роль вы прежде исполняли в ходе мероприятий MAST",
    "mast_role_public" => "Исполнял(а) следующие роли MAST мероприятий",
    "teamwork" => "Как часто вы работаете в команде?",
    "teamwork_public" => "Как часто работает в команде",
    "org" => "Представителем какой организации вы являетесь?",
    "org_public" => "Является представителем организации",
    "ref_person" => "Фамилия/Имя представителя",
    "ref_email" => "Электронная почта представителя",
    "mast_facilitator" => "Проходили ли вы обучение относительно групповой работы в MAST?",
    "mast_facilitator_public" => "Проходил(a) обучение относительно групповой работы в MAST",
    "education" => "Если у вас есть духовное образование, отметьте нужное",
    "education_public" => "Духовное образование",
    "ed_area" => "Выберите область, которая относится к вам",
    "ed_area_public" => "Области",
    "ed_place" => "Образовательное учреждение",
    "orig_langs" => "Оцените свой уровень владения исконными языками Библии",
    "orig_langs_public" => "Уровень владения исконными языками",
    "hebrew_knwlg" => "Древнееврейский",
    "greek_knwlg" => "Древнегреческий",
    "church_role" => "Какое служение вы исполняете в Церкви?",
    "church_role_public" => "Служение, исполняемое в Церкви",
    "show_langs_window" => "Нажмите кнопку с “Плюсом”, чтобы добавить языки",
    "select_search_lang_option" => "Выберите из списка или используйте поиск",
    "select_lang_option" => "Выберите язык",
    "resend_activation_title" => "Повторная отправка кода активации",
    "create" => "Создать",
    "apply_filter" => "Искать",
    "clear_filter" => "Очистить фильтр",
    "search_name_filter" => "Искать по имени",
    "filter" => "Фильтр",
    "all_members" => "Поиск пользователей",
    "all_mems" => "Все",
    "search_more" => "Загрузить еще...",
    "proj_lang_select" => "Выберите язык проекта",
    "select_project" => "Введите проект",
    "lang_select" => "Выберите язык",
    "proj_lang_public" => "Язык проекта",
    "projects_public" => "Проект",
    "show_rubric" => "Рубрика",
    "choose_event_level" => "Уровень проверки",

    // Passwordreset method
    "passwordreset_title" => "Сброс пароля",
    "continue" => "Далее",
    "enter_email" => "Введите Email",
    "passwordreset_link_message" => "Чтобы сбросить пароль, перейдите по ссылке {link}",
    "url_use_problem_hint" => "Если у вас проблемы с переходом по этому адресу, просто вставьте его в адресную строку браузера.",

    // Success messages
    "update_profile_success" => "Профиль был успешно обновлён!",
    "pwresettoken_send_success" => "Вам было отправлено письмо с инструкциями по сбросу пароля. Если его нет, проверьте в папке спам.",
    "password_reset_success" => "Ваш пароль был успешно изменен. Теперь вы можете войти, используя новый пароль",
    "account_activated_success" => "Учетная запись активирована. Теперь вы можете войти",
    "resend_activation_success_message" => "Сообщение с кодом активации отправлено на вашу электронную почту. Если письма нет во входящих, проверьте в папке спам.",
    "activation_link_message" => "<h3>Спасибо за регистрацию!</h3>\n Для активации вашего аккаунта, пожалуйста, нажмите на ссылку {link}",
    "registration_success_message" => "Регистрация прошла успешно! Проверьте почту для активации учетной записи. Если письма нет во входящих, проверьте в папке спам.",
    "registration_local_success_message" => "Регистрация прошла успешно!",
    "contact_us_successful" => "Спасибо! Ваше сообщение было отправлено нашим администраторам.",

    // Error messages
    "userName_characters_error" => "Имя пользователя должно содержать только маленькие буквы латиницы и числа, а также начинаться с букв",
    "userName_length_error" => "Имя пользователя должно быть длиной от 5 до 20 символов",
    "firstName_length_error" => "Имя должно быть длиной от 2 до 20 символов",
    "lastName_length_error" => "Фамилия должна быть длиной от 2 до 20 символов",
    "enter_valid_email_error" => "Введите правильный почтовый адрес",
    "email_taken_error" => "Почтовый адрес уже используется",
    "username_taken_error" => "Имя пользователя уже используется",
    "password_short_error" => "Пароль должен содержать 6 или более символов",
    "passwords_notmatch_error" => "Пароли не совпадают",
    "tou_accept_error" => "Вы должны принять Условия Использования",
    "sof_accept_error" => "Вы должны принять Утверждение Веры",
    "update_profile_error" => "Профиль не может быть обновлен",
    "token_expired_error" => "Код сброса пароля просрочен",
    "update_table_error" => "Ошибка записи в базу данных. Пожалуйста, попробуйте снова.",
    "no_account_error" => "Учетная запись не существует или код недействителен",
    "account_activated_error" => "Аккаунт уже активирован",
    "invalid_link_error" => "Неверная ссылка",
    "userType_wrong_error" => "Тип пользователя не верен",
    "choose_filter_option" => "Выберите хотя бы одну опцию фильтра для поиска пользователей",
    "empty_profile_error" => "Профиль не заполнен",
    "not_facilitator_error" => "Вы можете отправлять сообщения только фасилитаторам",
    "facilitator_yourself_error" => "Вы не можете отправлять сообщения себе",
    "delete_warning" => "Впишите название книги и нажмите кнопку \"Удалить\" для удаления книги",
    "lang_input_not_allowed" => "Режим \"Ввод Писания\" доступен только книг ULB 1-го уровня",

    // ------------ Events Controller ------------- //

    "admin_events_title" => "Cоздание и редактирование книг",
    "admin_members_title" => "Управление пользователями",
    "gw_language" => "Ключевой язык",
    "book_project" => "Исходное Писание",
    "book_tn" => "Исходные заметки",
    "book_tq" => "Исходные вопросы",
    "book_tw" => "Исходные слова",
    "udb" => "Динамическая Библия",
    "ulb" => "Дословная Библия",
    "sun" => "Универсальные символические условные обозначения ",
    "odb" => "Наш ежедневный хлеб",
    "rsb" => "Русская Синодальная Бибилия",
    "avd" => "ﺎﻠﻜﺗﺎﺑ ﺎﻠﻤﻗﺪﺳ ﺏﺎﻠﻠﻏﺓ ﺎﻠﻋﺮﺒﻳﺓ، ﻑﺎﻧ ﺩﺎﻴﻛ",
    "kar" => "Karoli Translation",
    "dkl" => "Danicic Karadzic Latin",
    "stf" => "Stefanović Novi Zavet",
    "src" => "Šarić Hrvatski",
    "ayt" => "SABDA’s Literal Bible",
    "tn" => "Заметки для перевода",
    "tq" => "Вопросы для перевода",
    "tw" => "Слова для перевода",
    "rad" => "РАДИО",
    "obs" => "Открытые Библейские Истории",
    "vsail" => "vSUN",
    "vsail_l2_l3" => "vSUN L{level} Check",
    "vsail_revision" => "vSUN Revision Check",
    "old_test" => "Ветхий Завет",
    "new_test" => "Новый Завет",
    "checker_apply" => "<b>{name}</b> готов(а) для проверки <b>{step}</b> книги <b>{book}</b>, главы <b>{chapter}</b> - <b>{language}</b> - <b>{project}</b> {level}",
    "checker_apply_tw" => "<b>{name}</b> готов(а) для проверки <b>{step}</b> категории <b>{book}</b>, группы <b>{chapter}</b> - <b>{language}</b> - <b>{project}</b> {level}",
    "apply_to_check" => "Начать проверку",
    "notifications" => "Уведомления",
    "see_all" => "Смотреть все",
    "no_notifs_msg" => "Нет уведомлений",
    "confirm_finished" => "Пожалуйста, убедитесь в том, что вы закончили работу в этом шаге",
    "confirm_yes" => "Все сделано",
    "next_step" => "Следующий шаг",
    "next_chapter" => "Следующая глава",
    "continue_alt" => "Продолжить",
    "side_by_side_toggle" => "Сравнительный обзор",
    "do_not_show_tutorial" => "Не показывать это окно в след. раз",
    "show_more" => "Показать больше",
    "step_num" => "Шаг {step_number}",
    "chapters" => "Главы",
    "chapter_number" => "Глава {chapter}",
    "devotion_number" => "Devotion {devotion}",
    "broadcast_number" => "Передача {broadcast}",
    "intro" => "Введение",
    "front" => "Введение",
    "chapter_not_started" => "Не начато",
    "chapter_in_progress" => "{number}% переведено",
    "chapter_finished" => "100% завершено",
    "chunks_number" => "{number} отрывков",
    "no_chunks_number" => "пока нет отрывков",
    "chunk_number" => "отрывок {chunk_number}",
    "chunk_finished" => "&nbsp;&nbsp;<span class=\"finished_msg glyphicon glyphicon-ok\"></span>",
    "checker_verb" => "Проверяющий (Пересказ)",
    "checker_peer" => "Проверяющий (Взаимопроверка)",
    "checker_kwc" => "Проверяющий (Проверка ключевых слов)",
    "checker_crc" => "Проверяющий (Проверка по стихам)",
    "step_status" => "Статус",
    "step_status_not_started" => "Не начато",
    "step_status_in_progress" => "В процессе",
    "step_status_waiting" => "Ожидание",
    "step_status_checked" => "В процессе",
    "step_status_finished" => "Пройден",
    "your_partner" => "Ваш партнёр",
    "your_checker" => "Ваш проверяющий",
    "your_translator" => "Ваш переводчик",
    "event_info" => "Прогресс книги",
    "progress" => "Прогресс",
    "manage" => "Управление",
    "event_participants" => "Участники книги",
    "status_online" => "в сети",
    "status_offline" => "не в сети",
    "go_back" => "Назад",
    "partner_tab_title" => "Партнер",
    "checking_tab_title" => "Проверяющий",
    "event_tab_title" => "Книга",
    "project_tab_title" => "Проект",
    "verses" => "Стихи",
    "show_keywords" => "tW",
    "show_questions" => "tQ",
    "show_notes" => "tN",
    "check" => "Проверка",
    "members_online_title" => "В сети",
    "help" => "ПОМОЩЬ",
    "demo" => "Демонстрация",
    "see_demo" => "Смотреть демонстрацию",
    "demo_video" => "Смотреть интро",
    "save" => "Сохранить",
    "read_chunk" => "Читать",
    "translate_chunk" => "Перевести",
    "partner_translation" => "Перевод партнера",
    "your_translation" => "Ваш перевод",
    "facilitator_events" => "Фасилитатация",
    "translator_events" => "Перевод",
    "l1_events" => "Проверка",
    "l2_3_events" => "Проверка L{level}",
    "book" => "Книга",
    "target_lang" => "Язык перевода",
    "project" => "Проект",
    "source" => "Исходный текст",
    "projects" => "Проекты",
    "current_step" => "Текущий шаг",
    "user" => "Пользователь",
    "draft1" => "Черновик 1",
    "state" => "Стадия",
    "level2_3_check" => "Проверка {level}-го уровня",
    "revision_check" => "Revision",
    "translators" => "Переводчики",
    "max_translators" => "Максимальное количество переводчиков",
    "checkers" => "Проверяющие",
    "apply" => "Участвовать",
    "apply_now" => "Подать заявку",
    "apply_as_translator" => "Участвовать как переводчик",
    "apply_as_checker" => "Участвовать как проверяющий {level}-го уровня",
    "state_started" => "Ожидание переводчиков",
    "state_translating" => "Переводится",
    "state_translated" => "Переведено",
    "state_l2_recruit" => "Waiting for revision checkers",
    "state_l2_check" => "Проверка ур.2",
    "l2" => "Revision",
    "l3" => "Проверка ур.3",
    "state_l2_checked" => "Проверка ур.2 завершена",
    "state_l3_recruit" => "Ожидание проверки ур.3",
    "state_l3_check" => "Проверка ур.3",
    "state_complete" => "Завершено",
    "checker_view" => "Вид проверяющего",
    "checker_other_view" => "Вид {0}-го проверяющего",
    "translator_view" => "Вид переводчика",
    "progress_all" => "Общий прогресс",
    "people_number" => "Людей: <span>{people_number}</span>",
    "add_person" => "Назначить главу",
    "add_translator" => "Добавить переводчиков/проверяющих",
    "add_checker" => "Добавить проверяющего",
    "assign_chapter_title" => "Назначить главу",
    "assign_group_title" => "Назначить группу",
    "assign" => "Назначить",
    "start_translation" => "Начать перевод",
    "start_checking" => "Начать проверку",
    "checkers_l2" => "Проверяющие 2-го уровня",
    "max_checkers_l2" => "Количество проверяющих 2-го уровня",
    "checkers_l3" => "Проверяющие 3-го уровня",
    "max_checkers_l3" => "Количество проверяющих 3-го уровня",
    "level" => "Уровень {0}",
    "cal_from" => "Событие начинается:",
    "cal_to" => "Событие заканчивается:",
    "reset_chunks" => "Сбросить отрывки",
    "make_chunk" => "Создать отрывок",
    "write_note_title" => "Напишите заметку к отрывку",
    "write_footnote_title" => "Добавить примечание",
    "all_notifications_title" => "Все уведомления",
    "video_call_title" => "Видео/Аудио Чат (бета)",
    "video_call" => "Видео вызов",
    "audio_call" => "Голосовой вызов",
    "turn_off_camera" => "Выключить камеру",
    "mute_mic" => "Выключить микрофон",
    "answer_call" => "Ответить",
    "hangup" => "Отменить вызов",
    "time_start" => "Начало",
    "time_end" => "Завершение",
    "chat" => "ЧАТ",
    "you_are_at" => "Вы на стадии",
    "8steps_vmast" => "8 Шагов MAST",
    "vmast" => "8 Шагов MAST",
    "lang_input" => "Ввод Писания",
    "notes" => "Заметки для перевода",
    "words" => "Слова для перевода",
    "questions" => "Вопросы для перевода",
    "level2" => "Уровень 2",
    "level3" => "Уровень 3",
    "l2_l3_vmast" => "MAST L{level} проверка",
    "revision_vmast" => "MAST Revision Check",
    "revision_sun" => "SUN Revision Check",
    "common" => "Общee",
    "vmast_events" => "V-MAST Активность",
    "create_event" => "Создать книгу",
    "edit_event" => "Редактировать книгу",
    "new_events" => "Новые события",
    "choose_project" => "Выберите проект",
    "create_project" => "Создать проект",
    "project_type" => "Тип проекта",
    "choose_gw_lang" => "Выберите ключевой язык",
    "choose_target_lang" => "Выберите целевой язык",
    "choose_source_trans" => "Выберите исходное Писание",
    "choose_source_tn" => "Выберите исходные заметоки",
    "choose_source_tq" => "Выберите исходные вопросы",
    "choose_source_tw" => "Выберите исходные слова",
    "choose_source_obs" => "Выберите исходник OBS",
    "choose_project_type" => "Выберите тип проекта",
    "gateway_languages" => "Проекты ключевых языков",
    "gateway_language" => "Ключевой язык",
    "create_gateway_language" => "Создать проект ключевого языка",
    "edit" => "Изменить",
    "add_admins_by_username" => "Внесите фасилитаторов по их именам пользователей",
    "admin_project_title" => "Cоздание проектов",
    "apply_checker_l1" => "Подписка на проверку",
    "not_available" => "Не доступно",
    "reset_markers" => "Сбросить маркеры",
    "remove_from_event" => "Удалить из книги",
    "event_status" => "Статус книги",
    "contributors" => "Участники",
    "event_contributors" => "Участники книги",
    "events_in_progress" => "Книги в процессе",
    "events_finished" => "Завершенные книги",
    "block" => "Заблокировать",
    "unblock" => "Разблокировать",
    "last_edit" => "Последнее редактирование",
    "noscript_message" => "Javascript выключен! Пожалуйста включите его, чтобы задействовать все функции v-mast.",
    "type_translation" => "Режим перевода",
    "type_checking" => "Режим проверки",
    "type_checking1" => "Режим проверяющего 1",
    "type_checking2" => "Режим проверяющего 2",
    "clear_cache" => "Обновить кэш",
    "update_cache_all" => "Обновить кэш (все книги)",
    "clear_cache_info" => "Обновить исходный текст в кэше.",
    "remove_checker" => "Удалить проверяющего",
    "chunk_verses" => "Стих(и): {0}",
    "verse_number" => "Стих {0}",
    "leaveit" => "Оставить как есть",
    "refresh" => "Обновить",
    "hide_help" => "Скрыть помощь",
    "show_help" => "Показать помощь",
    "copy" => "Копировать",
    "translation_event" => "Перевод:",
    "l2_event" => "2-ой уровень проверки",
    "source_text" => "Исходный текст",
    "target_text" => "Целевой текст",
    "import_translation_tip" => "Импортировать перевод",
    "import_options" => "Опции импортирования",
    "import_from_dcs" => "Импортировать из DCS (Door43)",
    "import_from_usfm" => "Импортировать из USFM (.usfm)",
    "import_from_ts" => "Импортировать из проекта translationStudio (.tstudio)",
    "import_from_zip" => "Импортировать tN, tQ или tW zip архив (.zip)",
    "repository_name" => "Название репозитория",
    "repository" => "Репозиторий",
    "updated_at" => "Дата обновления",
    "cancel" => "Отмена",
    "sail_dictionary" => "Словарь SAIL",
    "filter_by_word" => "Фильтровать по слову",
    "copy_symbol_tip" => "Нажмите, чтобы скопировать символ в буфер обмена",
    "copied_tip" => "Скопировано!",
    "show_dictionary" => "Показать словарь",
    "sail_enter_symbol" => "Символ",
    "sail_enter_word" => "Слово",
    "sail_word_exists" => "Слово существует",
    "sun_font_uploader" => "Загрузчик шрифтов SUN",
    "upload" => "Загрузить",
    "update" => "Обновить",
    "font_uploader_tooltip" => "Загружайте только шрифты SUN и BACKSUN, по одному за раз. Файлы шрифтов должны иметь формат WOFF (Web Open Font) и называться соответственно sun.woff и backsun.woff",
    "saildic_uploader_tooltip" => "Загружайте словарь SUN в формате .csv (разделение запятой). В следующем порядке: Символ, запятая, слово. Документ должен быть сохранен, используя кодировку UTF-8 (Unicode)",
    "remove_checker" => "Remove Checker",
    "bible_peer_checker" => "Peer Checker",
    "bible_verb_checker" => "Verbalize Checker",
    "bible_keyword_checker" => "Keyword Checker",
    "bible_vbv_checker" => "Verse-by-Verse Checker",
    "bible_final_checker" => "Verse Markers Step",
    "sun_theo_checker" => "Теологический проверяющий",
    "sun_odb_theo_checker" => "ODB проверяющий",
    "sun_vbv_checker" => "Проверяющий по стихам",
    "sun_odb_vbv_checker" => "SUN проверяющий",
    "other_checker" => "Первый проверяющий",
    "other_peer_checker" => "Парный проверяющий",
    "l2_snd_checker" => "Проверяющий 2-ой проверки",
    "l2_sun_peer_checker" => "Теологический проверяющий",
    "l2_p1_checker" => "1-ый парный проверяющий",
    "l2_p2_checker" => "2-ой парный проверяющий",
    "l3_p_checker" => "Проверяющий",
    "news" => "Новости",
    "draft" => "Черновик",
    "create_words_group" => "Создание группы слов",
    "create_group" => "Создать группу",
    "word_group_hint" => "Выделите слова, чтобы создать группу, удерживая CTRL + левая кнопка мыши или SHIFT + правая кнопка мыши",
    "group_id" => "Группа {0}",
    "user_has_group_error" => "Вы не можете удалить эту группу, так как она назначена переводчику.",
    "tw_translate_hint" => "Внимание: Переводитк только Ключевые слова, Определения/Факты (Definitions/Facts) и Варианты перевода (Translation Suggestions).",
    "compare" => "Режим сравнения",
    "common_tools" => "Общее",
    "sun_tools" => "vSUN",
    "faq_tools" => "FAQ",
    "faq_enter_question" => "Введте вопрос",
    "faq_enter_answer" => "Введите ответ",
    "filter_by_search" => "Искать вопрос",
    "faq_title" => "Часто задаваемые вопросы",
    "filter_by_category" => "Фильтровать по категории",

    // Steps
    "pray" => "Подготовка: Молитва",
    "pray_desc" => "<li><b>ЦЕЛЬ:</b> попросить у Господа помощи для осуществления перевода.</li><li>Молитесь столько времени, сколько вы считаете необходимым для этого шага, но постарайтесь провести в молитве, по крайней мере, 5-10 минут.</li><li>Этот шаг не менее важен, чем все последующие.</li>",
    "pray_text" => "Бог сотворил все языки и дал нам способность к их изучению и использованию. Господь наделил нас Святым Духом, чтобы Он во всем нам помогал. Поэтому начните свою работу с молитвы: прославьте Бога и попросите Его дать вам мудрость и водительство, необходимые для того, чтобы точно и правильно передать Его Слово.",
    "prep_pray_text" => "<strong>Поздравляем!</strong> Вы закончили вашу главу!  Отдохните, поспите, выпейте кофе или чай, и приступайте к следующей главе!",
    
    "pray_tn" => "Подготовка: Молитва",

    "multi-draft_lang_input" => "Ввод",
    "multi-draft_lang_input_desc" => "<li><b>PURPOSE:</b> to enter text digitally that has already been translated using the MAST 8 steps.</li>
                                        <li>Type or copy/paste translated text into the blanks verse by verse.</li>
                                        <li>To add a verse, click the green \"+\" at the bottom of the page.</li>
                                        <li>To delete a verse, click the red \"x\" at the bottom of the page.</li>
                                        <li>When every verse in the chapter has been filled, click \"Yes, I did\" and \"{step}\" to move to the self-edit step.</li>",

    "self-check_lang_input_desc" => "<li><b>PURPOSE:</b> to edit your draft and check the meaning of the source text's 
                                        accuracy while maintaining the blind draft's naturalness.</li>
                                    <li>Look for spelling, punctuation, and flow/naturalness errors.</li>
                                    <li>Look for any missing portions of text or missing/added facts.</li>
                                    <li>Check in detail the accuracy of your translation.</li>
                                    <li>To add or edit a footnote for this chapter:
                                        <ol>
                                            <li>To add a footnote, click in the translated text where you want to add the footnote.</li>
                                            <li>Click the footnote icon ( <i class='mdi mdi-bookmark'></i> ) on the right side of the translated text.</li>
                                            <li>Click on \"ft\" to add footnote text.</li>
                                            <li>To add an alternate text, click \"ft\" to add the text that explains the footnote, 
                                                then click \"fqa\" to add the alternate text.</li>
                                            <li>Click \"Save\" when text is added.</li>
                                            <li>You will see your added footnote in blue highlight inserted in the translated text.</li>
                                            <li>To edit the footnote, click on the footnote you want to edit and click the footnote 
                                                icon and it will pop up the window for you edit the footnote.</li>
                                            <li>To delete a footnote, click the \"x\" next to it. Click \"Save\" to save your changes.</li>
                                        </ol>
                                    </li>
                                    <li>Do not delete or re-write the translated text. You will lose the naturalness of the language.</li>
                                    <li>If you have additional thoughts or questions about your translation, click the note 
                                        icon <br>(<span class='mdi mdi-lead-pencil'></span>) next to the chunk, add the note, and click Save. 
                                        The peer, keyword, verse-by-verse checkers will see this note and will help to resolve the question or problem.</li>
                                    <li>When all changes and notes have been made, click \"Yes, I did\" and \"{step}\" to move to the next step.</li>",

    "consume" => "Чтение",
    "consume_desc" => "<li><b>ЦЕЛЬ:</b> подготовить свою кратковременную память к переводу текста</li><li>Прочитайте текст от начала до конца. Попытайтесь как можно более глубоко в него вникнуть; посмотрите, о чем идет речь; постарайтесь полностью понять содержание текста.</li><li>Останавливайтесь, размышляйте и перечитывайте текст по мере необходимости.</li><li>Уделите этому шагу не более 12 минут.</li>",

    "consume_odb" => "Чтение",
    "consume_sun_desc" => "<li><b>ЦЕЛЬ:</b> получить общую картину главы</li><li>Прочитайте текст от начала до конца. Попытайтесь как можно более глубоко в него вникнуть; посмотрите, о чем идет речь; постарайтесь полностью понять содержание текста.</li><li>Останавливайтесь, размышляйте и перечитывайте текст по мере необходимости.</li><li>Уделите этому шагу не более 12 минут.</li>",

    "consume_tn" => "Чтение Писания",
    "consume_tn_full" => "Чтение Писания на целевом языке",
    "consume_tn_desc" => "<li><b>PURPOSE:</b> to intake source text to prepare for translating notes.</li>
                            <li>Read the assigned chapter carefully.</li>
                            <li>Try to understand the meaning and details of the text to get a general overview.</li>
                            <li>If you see the paper icon (<span class='mdi mdi-bookmark'></span>), hover over 
                                it with your mouse.  It is a footnote to help you translate the (verse or note).</li>
                            <li>Take no more than 7-10 minutes on this step.</li>
                            <li>Click \"Yes I did\" and \"{step}\" to move to the next step.</li>",

    "consume_rad_desc" => "<li><b>ЦЕЛЬ:</b> чтение текста для подготовки к переводу.</li>
                            <li>Тщательно прочитайте назначенную трансляцию.</li>
                            <li>Попробуйте понять смысл и детали текста для общего обзора.</li>
                            <li>Нажмите \"Да, я сделал\" и \"{step}\", чтобы перейти к следующему шагу.</li>",

    "verbalize" => "Пересказ",
    "verbalize_desc" => "<li><b>ЦЕЛЬ:</b> словесно выразить то, о чем вы размышляли, чтобы задействовать вашу кратковременную память.</li><li>Свяжитесь со своим напарником, используя наиболее удобные для вас средства связи: Skype, Hangout, V-MAST чат, телефон и т.д.</li><li>Не обсуждайте прочитанный текст, а перескажите его: «Я прочел о том, что ...», и затем «Основная мысль это ...».</li><li>Лучше всего пересказывать текст на родном языке.</li><li>Уделите этому шагу с напарником не более 7-10 минут.</li>",

    "chunking" => "Отрывок",
    "chunking_sun" => "Мысленный отрывок",
    "chunking_desc" => "<li><b>ЦЕЛЬ:</b> разбить текст на смысловые отрывки для перевода.</li><li>Выделите 2-5 стиха по порядку и нажмите «создать отрывок», чтобы разделить текст на отрывки, необходимые для следующего шага.</li><li>Разбейте, таким образом, весь текст, объединяя стихи по смыслу, принимая в расчет логические паузы, существующие между ними.</li><li>Уделите этому шагу не более 5 минут.</li>",

    "chunking_sun_desc" => "<li><b>ЦЕЛЬ:</b> разбить текст на смысловые отрывки для перевода.</li><li>Выделите 2-5 стиха по порядку и нажмите «создать отрывок», чтобы разделить текст на отрывки, необходимые для наброска вслепую.</li><li>Разбейте, таким образом, весь текст, объединяя стихи по смыслу, принимая в расчет логические паузы, существующие между ними.</li><li>Уделите этому шагу не более 5 минут.</li>",

    "read-chunk" => "Набросок вслепую",
    "read-chunk-alt" => "Чтение отрывка",
    "read-chunk_desc" => "<li><b>ЦЕЛЬ:</b> рассмотреть отрывок, чтобы подготовиться к наброску вслепую.</li><li>Прочтите и мысленно вникните в отрывок текста, попросив помощи у Святого Духа для понимания содержания.</li><li>Уделите каждому отрывку не более 5 минут.</li>",
    
    "read-chunk_tn" => "Чтение отрывка",
    "read-chunk_tn_desc" => "<li><b>PURPOSE:</b> to read through the chunk of Notes you will translate.</li>
                                <li>Read the assigned chunk of Notes carefully.</li>
                                <li>Try to understand the meaning and details of the text to get a general overview.</li>
                                <li>Take about 5 minutes on this step.</li>
                                <li>Click \"Yes I did\" and \"{step}\" to move to the next step.</li>",
    
    "blind-draft" => "Набросок вслепую",
    "blind-draft-alt" => "Набросок вслепую",
    "blind-draft_previous" => "Набросок вслепую - предыдущий отрывок",
    "blind-draft_desc" => "<li><b>ЦЕЛЬ:</b> перевести исходный текст как можно более естественно на целевой язык.</li><li>Не смотря в исходный текст, запишите ваш перевод отрывка как можно точнее и естественее, используя понятные и ясные слова.</li><li>Не смотрите на исходный текст. Просто переводите то, что запомнили.</li><li>Не беспокойтесь о нумерации стихов. Вы сделаете это на последнем шаге.</li><li>Одна из основных характеристик хорошего перевода — это естественность языка. Слепой набросок  отражает эту характеристику и позволяет мозгу/кратковременной памяти быстро вспомнить текст простыми и благозвучными словами. Это не значит, что текст будет идеально точным (следующие четыре шага помогут нам в этом), но это значит — заложить основу естественности, которая, если отсутствует, приводит к распространненой ошибке и длительным периодам исправления основополагающей ошибки.</li><li>Уделите каждому отрывку не более 10-12 минут.</li>",

    "blind-draft_tn" => "Набросок вслепую",
    "blind-draft_tn_desc" => "<li><b>PURPOSE:</b> to review the chunk of scripture and Notes and then \"blind translate\" the Note in the target language.</li>
                                <li>Read the scripture and Note, keeping the context of the whole chapter mind.</li>
                                <li>Think through the scripture and Note in the target language.</li>
                                <li>Switch to the \"Translate\" tab and blind draft as much of the Note in the target language as you can.</li>
                                <li>Switch back and forth between the \"Read\" and \"Translate\" tab as much as needed.</li>
                                <li>As much as possible, translate phrase by phrase and not word for word, to preserve naturalness of the target language.</li>
                                <li>Type or copy and paste the scripture, and then translate the Note that goes with the scripture on the next line.</li>
                                <li>Format the text by double clicking the line to format and selecting the format. 
                                    <ol>
                                        <li>Scripture is the larger text. To format double click and select \"Header 1.\"</li>
                                        <li>The translated Note is the smaller text. To format double click and select \"Normal.\"</li>
                                        <li>To format subtitles in  “Intro” sections, double click and select \"Header 4.\"</li>
                                    </ol>
                                </li>
                                <li>No need to translate links.</li>
                                <li>Click \"Yes I did\" and \"{step}\" to move to the next chunk to translate until the chapter is finished.</li>",

    "multi-draft_rad_desc" => "<li><b>PURPOSE:</b> to translate the text as naturally as possible while keeping the meaning.</li>
                                <li>As much as possible, translate phrase by phrase and not word-for-word, to preserve naturalness of the target language.
                                    <ol>
                                        <li>Translate the word \"Broadcast\" and the number.</li>
                                        <li>Translate the name of the broadcast.</li>
                                        <li>Translate each character name in ALL CAPS by turning on Caps Lock and typing 
                                            the name. It will go fast if you copy and then paste the name into the proper 
                                            blanks for each character.</li>
                                        <li>Translate the text the speaker is saying as naturally as you can.</li>
                                    </ol>
                                </li>
                                <li>Click \"Yes, I did\" and \"{step}\" to move to the next step.</li>
                                <li>Make sure the save icon is not flashing when you click \"{step}\".</li>",

    "self-check" => "Самопроверка",
    "self-check_desc" => "<li><b>ЦЕЛЬ:</b> отредактировать свой набросок вслепую и внимательно сравнить значение исходного текста, при этом сохраняя форму и структуру источника.</li><li>Обратите внимание на грамматические ошибки, пунктуацию, благозвучность/естественность. </li><li>Обратите внимание на каждую пропущенную вами часть текста, а также на упущенные или добавленные вами факты.</li><li>Не преувеличивайте, не упрощайте и не добавляйте текст для лучшего пояснения. Если вы считаете, что дополнительное пояснение необходимо, тогда запишите ваше замечание к данному отрывку, используя редактор заметок, и продолжите перевод.</li><li>Уделите этому шагу не более 15-20 минут.</li>",

    "self-check_tn" => "Самопроверка",
    "self-check_tn_desc" => "<li><b>PURPOSE:</b> to edit your Notes translation and check accuracy while maintaining naturalness.</li>
                                <li>Look for spelling, punctuation, flow/naturalness errors.</li>
                                <li>Look for any missing portions of text or missing or added facts.</li>
                                <li>Check the accuracy of your translation in detail.</li>
                                <li>Any resources can be used.</li>
                                <li>Do not delete or re-write the translated text.  You will lose the naturalness of language.</li>
                                <li>If you have additional thoughts or questions about your translation, click the note 
                                    icon <br>(<span class='mdi mdi-lead-pencil'></span>) next to the chunk and add the 
                                    note and click save. The checkers will see this note and help to resolve the question or problem.</li>
                                <li>This should take about 30 minutes.</li>
                                <li>When all changes and notes have been made, click \"Yes I did\" and \"{step}.\" </li>
                                <li>WARNING: This is the last chance you will have to change your translation.  
                                    Do not click \"Yes, I did\" and \"{step}\" until you have made all changes.</li>",
    "self-check_tn_chk" => "Правка заметок",
    "self-check_tn_chk_desc" => "<li><b>PURPOSE:</b> to edit the Notes translation and check accuracy while maintaining naturalness.</li>
                                    <li>Look for spelling, punctuation, flow/naturalness errors.</li>
                                    <li>Look for any missing portions of text or missing or added facts.</li>
                                    <li>Check the accuracy of translation in detail.</li>
                                    <li>Make sure to look at notes on the chunk of the translation Note.</li>
                                    <li>Any resources can be used.</li>
                                    <li>Do not delete or re-write the translated text.  You will lose the naturalness of language.</li>
                                    <li>If you have additional thoughts or questions about the translation, click the note 
                                        icon <br>(<span class='mdi mdi-lead-pencil'></span>) next to the chunk and add 
                                        the note and click save. The 2nd checker will see this note and help to resolve the question or problem.</li>
                                    <li>This should take about 30 minutes.</li>
                                    <li>When all changes and notes have been made, click \"Yes I did\" and \"{step}.\"</li>",

    "self-check_rad_desc" => "<li><b>PURPOSE:</b> to edit your draft and check meaning and accuracy while maintaining 
                                naturalness.</li>
                                <li>Look for spelling, punctuation, and flow/naturalness errors.</li>
                                <li>Look for any missing portions of text or missing/added facts.</li>
                                <li>Check in detail the accuracy of your translation.</li>
                                <li>If you have additional thoughts or questions about your translation, click the note 
                                    icon <br>(<span class='mdi mdi-lead-pencil'></span>) next to the chunk, add the note, 
                                    and click Save. The peer-editor will see this note and will help to resolve the 
                                    question or problem.</li>
                                <li>When all changes and notes have been made, click \"Yes, I did\" and \"{step}.\"</li>
                                <li>WARNING: THIS IS THE LAST CHANCE TO MAKE CHANGES TO THE TRANSLATION. DO NOT MOVE TO NEXT STEP UNTIL THE SELF-EDIT IS COMPLETED!</li>",

    "highlight_tn" => "Выделить",
    "highlight_tn_full" => "Выделить сложные места",
    "highlight_tn_desc" => "<li><b>PURPOSE:</b> to highlight passages that are considered theologically difficult.</li>
                            <li>Read the assigned chapter carefully.</li>
                            <li>Double click to highlight words or select a phrase by left clicking and selecting the phrase. 
                                <ol>
                                    <li>This is not a word or phrase that you think needs a Translation Note.</li>
                                    <li>It is to denote a particularly difficult passage you want to make sure has a Note.</li>
                                </ol>
                            </li>
                            <li>Read through entire chapter and highlight as needed.</li>
                            <li>Take no more than 7-10 minutes on this step.</li>
                            <li>Click \"Yes I did\" and \"{step}\" to move to the next step.</li>",

    "peer-review" => "Взаимопроверка",
    "peer-review_desc" => "<li><b>ЦЕЛЬ:</b> утвердить с другим носителем целевого языка текст на верное и естественное  изложение исходного текста (таким же образом, как и в самопроверке).</li><li>Ваш перевод будет рассмотрен проверяющим.</li><li>Проверяющий рассмотрит ваш перевод, обратив внимание на все, что было пропущено или добавлено, а также на то, что сильно отличается от исходного текста.</li><li>Проверяющий также рассмотрит, являются ли подобранные слова благозвучными, правильными и понятными.</li><li>Проверяющий должен уделить этому шагу не более 30-45 минут.</li><li>Когда проверяющий  закончит отмечать свои примечания и наблюдения, свяжитесь с ним, используя Skype, Hangout, V-MAST чат, телефон и т.д.</li><li>Обсудите его/их замечания. В ходе обсуждения с вашим проверяющим внесите необходимые изменения в ваш перевод.</li><li>Убедитесь, что подобранные слова соответствуют выбранному литературному уровню/стилю.</li><li>Уделите этому упражнению с вашим напарником не более 60 минут. Не тратьте время на разногласия. В таких случаях, оставьте текст как таковой, запишите примечание к данному отрывку и продолжите перевод.</li>",

    "peer-review_checker_desc" => "<li><b>ЦЕЛЬ:</b> утвердить переведенный текст на верное и благозвучное изложение исходного текста (таким же образом, как и в самопроверке).</li><li>Так как вы проверяющий, инструкции для вас будут выделены оранжевым цветом.</li><li>Ваша задача проверить текст одного из ваших напарников, и один из них будет проверять ваш перевод, если вы также являетесь переводчиком.</li><li>Проверьте перевод вашего напарника, обратив внимание на все, что было пропущено или добавлено, а также на то, что сильно отличается от исходного текста.</li><li>Обратите внимание на выбранные слова в переводе, которые не являются благозвучными, ясными и понятными.</li><li>Когда вы закончите отмечать свои примечания и наблюдения, свяжитесь с переводчиком, используя Skype, Hangout, V-MAST чат, телефон и т.д.</li><li>Обсудите его/их замечания.</li><li>Убедитесь, что использованные слова соответствуют выбранному литературному уровню/стилю.</li><li>Уделите этому упражнению с вашим напарником не более 60 минут. Не тратьте время на разногласия. В таких случаях, оставьте текст как таковой, запишите примечание к данному отрывку и продолжайте.</li>",

    "peer-review_tn" => "2<sup>ая</sup> проверка",
    "peer-review_tn_desc" => "<li><b>PURPOSE:</b> to check the Notes translation and check accuracy while maintaining naturalness.</li>
                                <li>You will work together with another checker to complete this step. This will be the last chance to make changes to the Notes.</li>
                                <li>Look for spelling, punctuation, flow/naturalness errors.</li>
                                <li>Look for any missing portions of text or missing or added facts.</li>
                                <li>Check the accuracy of translation in detail.</li>
                                <li>Make sure to look at notes on the chunk of the translation Note.</li>
                                <li>Any resources can be used.</li>
                                <li>Do not delete or re-write the translated text. You will lose the naturalness of language.</li>
                                <li>When you and the 2nd checker have completed the check, the checker will contact you to discuss 
                                    recommended changes via: Skype, Messenger, WhatsApp, phone, etc…</li>
                                <li>You and the 2nd checker should discuss any changes they think should be made. 
                                    If you agree to the changes, you should make the changes right away. If you do not agree, 
                                    contact your facilitator to discuss the disagreement. 
                                    DO NOT continue to the next step until disagreements are resolved.</li>
                                <li>This should take about 30 minutes.</li>
                                <li>When all changes and notes have been made, click \"Yes I did\" and \"{step}.\" </li>
                                <li>WARNING: This is the last chance you will have to check the Notes.  
                                    Do not click \"Yes, I did\" and \"{step}\" until you have made all changes you feel are necessary.</li>",

    "peer-review_tn_chk_desc" => "<li><b>PURPOSE:</b> to check the Notes translation and check accuracy while maintaining naturalness.</li>
                                    <li>You will work together with another checker to complete this step. This will be the last chance to make changes to the Notes.</li>
                                    <li>You will see all changes the 1st checker made to the Notes. 
                                        <ol>
                                            <li>Text added to the Notes will be highlighted in green.</li>
                                            <li>Text deleted from the Notes will be highlighted in red/pink.</li>
                                        </ol>
                                    </li>
                                    <li>Look for spelling, punctuation, flow/naturalness errors.</li>
                                    <li>Look for any missing portions of text or missing or added facts.</li>
                                    <li>Check the accuracy of translation in detail.</li>
                                    <li>Make sure to look at notes on the chunk of the translation Note.</li>
                                    <li>Any resources can be used.</li>
                                    <li>If you have recommended changes, click the note icon (<span class='mdi mdi-lead-pencil'></span>) next 
                                        to the chunk and add the note and click save.</li>
                                    <li>When you have completed the check, contact the 1st checker to discuss recommended 
                                        changes via: Skype, Messenger, WhatsApp, phone, etc…</li>
                                    <li>You and the 1st checker should discuss any changes you think should be made. 
                                        If you agree to the changes, the 1st checker should make the changes right away. 
                                        If you do not agree, contact your facilitator to discuss the disagreement. 
                                        DO NOT continue to the next step until disagreements are resolved.</li>
                                    <li>This should take about 30 minutes.</li>
                                    <li>When all changes and notes have been made by the 1st checker, click \"Yes I did\" and \"{step}.\"</li>
                                    <li>WARNING: This is the last chance you will have to check the Notes. Do not 
                                        click \"Yes, I did\" and \"{step}\" until you have made all changes you feel are necessary.</li>",

    "peer-review_rad_desc" => "<li><b>PURPOSE:</b> to check the text accuracy while maintaining naturalness.</li>
                                <li>You will look for spelling, punctuation, and flow/naturalness errors.</li>
                                <li>You will look for any missing portions of text or missing or added facts.</li>
                                <li>You will check the accuracy of the translation in detail.</li>
                                <li>Make sure to look at translators notes by clicking on the note icon with a number 
                                    above it next to the text box (<span class='mdi mdi-lead-pencil'></span>)</li>
                                <li>When all changes have been made click \"Yes, I did\" and \"{step}\" to complete the check.</li>",

    "keyword-check" => "Проверка ключевых слов",
    "keyword-check_sun" => "Проверка ключевых слов",
    "keyword-check_desc" => "<li><b>ЦЕЛЬ:</b> проверить, что все важные слова присутствуют в переводе и что все они переведены правильно.</li><li>Проверяющий будет видеть только исходный текст.</li><li>Проверяющий должен выделить ключевые слова в исходном тексте. Выделенные слова также будут видны у вас в исходном тексте.</li><li>Ключевыми словами являются: местоимения, имена, числа и все важные термины.</li><li>Свяжитесь с вашим проверяющим, используя Skype, Hangout, V-MAST чат, телефон и т.д.</li><li>Проверяющий должен просмотреть каждый стих и спросить, переведено ли вами выделенное слово. Вы можете просто отвечать: «Да» или «Нет». Проверяющий также может спросить у вас, как вы перевели определенные слова.</li><li>Внесите необходимые поправки в ваш текст.</li><li>Убедитесь, что использованные слова соответствуют выбранному литературному уровню/стилю.</li><li>Уделите этому шагу не более 30 минут. Не тратьте время на разногласия. В таких случаях, оставьте текст как таковой, запишите примечание к данному отрывку и продолжите перевод.</li>",

    "keyword-check_checker_desc" => "<li><b>ЦЕЛЬ:</b> убедиться, что значимые слова присутствуют в переведенном тексте и верно выражены.</li><li>Так как вы проверяющий, инструкции для вас будут выделены оранжевым цветом.</li><li>Когда вы начнете проверять работу другого переводчика, вы сможете видеть только исходный текст.</li><li>Вы можете выделить все ключевые слова в исходном тексте двойным нажатием на самом слове или выделением слова/фразы при помощи мыши. Переводчик также может видеть выделенные слова в исходном тексте.</li><li>Ключевыми словами являются: местоимения, имена, числа и все важные термины.</li><li>Свяжитесь с вашим напарником, используя Skype, Hangout, V-MAST чат, телефон и т.д.</li><li>Просмотрите каждый стих и проверьте каждое выделенное слово с переводчиком. Переводчик может отвечать либо «да», либо «нет». Вы также должны спросить у переводчика, как он перевел некоторые важные термины. Обращайте внимание на крупные ошибки и важные упущения.</li><li>Просмотрите все примечания, относящиеся к стиху, и попытаться решить сложности с ключевыми словами.</li><li>Не старайтесь анализировать или критиковать текст, сосредоточьте ваше внимание только на тех словах, которые несут более важное значение.</li><li>Уделите этому шагу не более 30 минут. Не тратьте время на разногласия. В таких случаях, оставьте текст как таковой, запишите примечание к данному отрывку и продолжайте.</li>",
    
    "keyword-check_tn" => "Проверить выделенное",
    "keyword-check_tn_full" => "Проверить выделенные заметки",
    "keyword-check_tn_desc" => "<li><b>PURPOSE:</b> to check the highlighted portions of scripture and compare it with the Note the highlight correlates to.</li>
                                <li>Look for highlighting in the scripture and look to make sure there is an accurate Note that reflects the scripture accurately.</li>
                                <li>Any resources can be used.</li>
                                <li>Do not delete or re-write the translated text.</li>
                                <li>If you have additional thoughts or questions about the translation, click the note 
                                    icon <br>(<span class='mdi mdi-lead-pencil'></span>) next to the chunk and add the note and click save. 
                                    The 2nd checker will see this note and help to resolve the question or problem.</li>
                                <li>This should take about 15-20 minutes.</li>
                                <li>When all changes and notes have been made, click \"Yes I did\" and \"{step}.\"</li>",

    "content-review" => "Проверка по стихам",
    "content-review_sun" => "Проверка по стихам",
    "content-review_odb" => "Проверка SUN",
    "content-review_desc" => "<li><b>ЦЕЛЬ:</b> убедиться, что каждый отрывок и глава точно передают то же содержание в целевом языке.</li><li>Свяжитесь с вашим проверяющим, используя Skype, Hangout, V-MAST чат, телефон и т.д.</li><li>Обзор можно провести двумя способами: <ol><li>Если проверяющий знает только язык исходного текста, то проверка должна быть осуществлена при помощи обратного перевода. В этом случае, вы будете читать переведенный текст, отрывок за отрывком, затем вы или другой человек должен перевести прочитанный отрывок на исходный язык, а проверяющий должен сравнивать то, что он слышит, с исходным текстом.</li><li>Если проверяющий свободно владеет обоими языками, то он может либо использовать первый способ проверки, либо сам сравнить ваш перевод с исходным текстом.</li></ol></li><li>В любом случае, проверяющий может задать вам вопросы относительно всех мест, которые, по его мнению, не переданы точно. В ходе обсуждения вносите все необходимые исправления.</li><li>Убедитесь, чтобы слова, использованные в переводе соответствовали выбранному литературному стилю (уровню языка).</li><li>Уделите данному шагу не более 30 минут. Не тратьте время на разногласия. В таких случаях, оставьте текст как таковой, запишите примечание к данному отрывку и продолжите работу.</li>",

    "content-review_checker_desc" => "<li><b>ЦЕЛЬ:</b> убедиться, что каждый отрывок и глава точно передают то же содержание в целевом языке.</li><li>Так как вы проверяющий, инструкции для вас будут выделены оранжевым цветом.</li><li>Свяжитесь с переводчиком, используя наиболее удобный для вас тип связи: Skype, Hangout, телефон и пр.</li><li>Обзор можно провести двумя способами (по умолчанию идет первый способ ее проведения): <ol><li>Если вы владеете только языком исходного текста, то проверка должна быть осуществлена при помощи обратного перевода. В этом случае, переводчик будет читать переведенный текст, отрывок за отрывком, затем переводчик или другой человек должен перевести прочитанный отрывок на исходный язык, и вы должны сравнивать то, что вы слышите с исходным текстом.</li><li>Если вы свободно владеете обоими языками, то вы можете либо использовать первый способ проверки, либо выбрать сравнительный обзор перевода с исходным текстом. В этом случае нажмите на кнопку «Сравнительный обзор», чтобы переключиться в режим показа обоих текстов. Затем сравните два текста самостоятельно.</li></ol></li><li>В любом случае, вы должны задать вопросы ко всему, что, по вашему мнению, не было переведено точно или полностью на целевой язык.</li><li>Уделите данному шагу не более 30 минут. Не тратьте время на разногласия. В таких случаях, оставьте текст как таковой, запишите примечание к данному отрывку и продолжите работу.</li>",
    
    "final-review" => "Нумерация стихов",
    "final-review_desc" => "<li><b>PURPOSE:</b> здесь вы можете проставить нумерацию к стихам в переведенном тексте.</li><li>Прочтите переведенный текст и сравните с исходным текстом, затем нажмите на нумерацию и перетащите ее к правильному стиху в переведенном тексте.</li><li>Уделите этому шагу не более 10 минут.</li>",
    "finished" => "Завершено",

    "rearrange" => "Перегруппировка",
    "rearrange-alt" => "Перегруппировка",
    "rearrange_previous" => "Перегруппировка - предыдущий отрывок",
    "rearrange_desc" => "<li><b>PURPOSE:</b> rearrange sentence structure to fit SUN rules.</li><li>Grammar Rules<ol><li>To make a word plural, type it in twice with no space in between. Example (things = thingthing) The exceptions are: (boys, sons, brothers, girls, daughters, sisters, children) These words have specific plurals and they are listed in the dictionary.</li><li>To make something possessive, type SHIFT 8 (the asterisk) then the space bar. This will put a dot on the upper right hand corner of the word.</li><li>All sentences must follow the following sentence structure:<ul><li>Subject, Verb, Direct Object</li><li>Adjectives before the noun</li><li>Adverbs before the verb</li><li>Possessives before the thing they possess (ex. house of God is written as God’s house.)</li></ul></li><li>All sentences must be no more than 7 symbols long. Exceptions are:<ul><li>Words in front of a quotation that indicate who is speaking do not count. Ex: Jesus said, \"I will go to your house today.\" This sentence is allowed because there is no more than 7 words inside the quotation. The words Jesus said do not count</li><li>Plurals count as one symbol (thingthing counts as one Symbol.)</li></ul></li><li>When an English word has multiple meanings, only use the meaning that is taught when the word is introduced (the meaning that matches the symbol). For example, the word break is taught as \"cutting into a whole.\" In English, the word break can also be used to mean, rest (take a break), disobey (break a law), etc. If you come across one of those other uses for break, use the real meaning of it (use rest or disobey) instead of break. Another example is the word \"with.\" \"I eat with a spoon\" would be translated, \"I eat use spoon.\"</li><li>Avoid starting a sentence with a conjunction like but or and. Use \"if\" only when a clear cause-effect relationship is expressed in the sentence.</li><li>Ordinal numbers are followed by a singular noun. Cardinal numbers are followed by a plural noun – EVEN if the number is 1. Example : \"Third day\" is written (three day). \"Three days\" is written (three dayday). \"First day\" is written (one day). \"One day\" is written (one dayday).</li><li>Similar concept for all/every.  If you want to say \"all day,\" you write \"all day.\"  If you want to say every day, you write \"all dayday.\"</li><li>Only use question words in a question. For example, don’t say, \"This is WHO he was talking about.\" Say instead, \"This is person he talk.\"</li><li>Be careful of interpreting vs. translating. When you translate, you are only saying what the text said. Do not interpret or paraphrase. That is the pastor’s job.</li><li>Avoid using filler words (conjunctions, prepositions, articles) unless their absence changes the meaning of the sentence. For example \"In the beginning God made the heavens and the earth.\" The words in blue are not needed. The word in red is needed in order for the reader to not think God turned the heavens into earth.</li></ol></li><li>Rules for Creating New Symbols<ol><li>When you come to a word that is not in the dictionary, try typing in synonyms to see if a similar word is in the dictionary.</li><li>If there are no synonyms and you need a new word, type it in English in your text.</li><li>Emely will create a new symbol and add it to the dictionary.</li><li>Once the new symbol is added to the dictionary, it will be posted in the News section to let you know that the word is now available.</li></ol></li>",

    "symbol-draft" => "Набросок символов",
    "symbol-draft_previous" => "Набросок символов - предыдущий отрывок",
    "symbol-draft_desc" => "<li><b>PURPOSE:</b> assign symbols for words.</li><li>Switch keyboard from English to SUN font by clicking the keyboard icon on the bottom right side of your screen and selecting SUN font.</li><li>When a word does not have a symbol, ask the following questions: <ol><li>Can the word be attached to existing symbol?</li><li>Can an extension be made instead of a new character?</li><li>If the answers to the above questions are negative, contact the SAIL Director to create a new symbol that follows the 3 golden rules of SUN (Simple, Intuitive, Universal), then type the word and continue the translation into symbols.  The word will be changed to a symbol in step 7.</li><li>Proper nouns are created using the meaning of the name or something significant about the person or place.</li></ol></li>",

    "self-check_sun" => "Peer Check",
    "self-edit_sun_desc" => "<li><b>PURPOSE:</b> do a literal backtranslation of your SUN Scripture.</li><li>Edit text if any errors or inconsistencies are found.</li>",

    "theo-check" => "Теологическая проверка",
    "theo-check_odb" => "ODB проверка",
    "theo-check_desc" => "<li><b>PURPOSE:</b> check the backtranslation for theological accuracy.</li><li>If you see an incorrect word, double-click the word to highlight it and put a comment in the \"notes\" for the suggested change.</li>",

    "content-review_sun_desc" => "<li><b>PURPOSE:</b> check every verse and correct the text according to the theological check.</li>",

    "consume_l2_desc" => "<li><b>PURPOSE:</b> to intake source text to become familiar with text you will check.</li>
                <li>Read the assigned chapter carefully in the source text AND in the target text.
                    Switch between source and target text by clicking on the tabs.</li>
                <li>Try to understand the meaning and details of the text to get a general overview.</li>
                <li>If you see the paper icon (<span class='mdi mdi-bookmark'></span>), hover over it with your mouse.  It is a translator’s note for that verse.</li>
                <li>This step should take about 10 minutes.</li>
                <li>Click \"Yes I did\" and \"{step}\" to move to the next step.</li>",

    "consume_sun_l2_desc" => "<li><b>PURPOSE:</b> to understand the context of the whole chapter.</li>
                <li>Read the assigned text in its entirety. Carefully read and consider what is being said to 
                    understand the full content of the passage.</li>
                <li>Pause, reflect, and re-read as necessary.</li>
                <li>Click \"Yes I did\" and \"{step}\" to move to the next step.</li>",

    "self-check_l2_desc" => "<li><b>PURPOSE:</b> to check the target text for accuracy while maintaining the naturalness of the 
                            language and <b>ensuring accurate and literal common language terms for \"Father\" and \"Son\" are 
                            used when referring to God the Father and Jesus Christ.</b></li>
                        <li><b>LENGTH:</b> Spend about 30 minutes on this step.</li>
                        <li>While checking, keep in mind the difference between accuracy and preference.</li>
                        <li>The primary purpose is to check the accuracy of the translation in detail.</li>
                        <li>The column on the left is the source text and the right is editable target text.</li>
                        <li>Any changes made on this step by the checker will be reflected in the next checking steps:
                            <ol>
                                <li>Added text will show in green.</li>
                                <li>Deleted text will show in red.</li>
                            </ol>
                        </li>
                        <li>Correct errors in spelling, punctuation, and flow/naturalness.</li>
                        <li>Check notes for each chunk to see what questions or corrections were made from the translation process (Level 1).
                            <ol>
                                <li>Notes are marked by a number next to the (<span class='mdi mdi-lead-pencil'></span>) icon.</li>
                                <li>Notes are viewed per \"chunk\", because they are carried from the translation process, 
                                    which is done in chunks rather than verses. </li>
                                <li>If you have questions or thoughts for the next checkers, leave a note for the chunk.</li>
                            </ol>
                        </li>
                        <li>Any resources can be used for checking.</li>
                        <li>To add or edit a footnote for this chapter:
                            <ol>
                                <li>To add a footnote, click in the translated text where you want to add the footnote.</li>
                                <li>Click the footnote icon ( <i class='mdi mdi-bookmark'></i> ) on the right side of the translated text.</li>
                                <li>Click on \"ft\" to add footnote text.</li>
                                <li>To add an alternate text, click \"ft\" to add the text that explains the footnote, 
                                    then click \"fqa\" to add the alternate text.</li>
                                <li>Click \"Save\" when text is added.</li>
                                <li>You will see your added footnote in blue highlight inserted in the translated text.</li>
                                <li>To edit the footnote, click on the footnote you want to edit and click the footnote 
                                    icon and it will pop up the window for you edit the footnote.</li>
                                <li>To delete a footnote, click the \"x\" next to it. Click \"Save\" to save your changes.</li>
                            </ol>
                        </li>
                        <li>When all changes and notes have been made, click \"Yes, I did\" and \"{step}\".</li>",

    "theo-check_sun_l2_desc" => "<li><b>PURPOSE:</b> to check the SUN translation for accuracy and consistency.</li>
                    <li>While checking, make sure that common phrases are being translated accurately and in the same way, using the same symbols.</li>
                    <li>Look for punctuation, flow/naturalness errors.</li>
                    <li>Look for missing portions of text or missing or added facts.</li>
                    <li>Keep in mind the difference between accuracy and preference.</li>
                    <li>Both SUN symbols and backSUN translation can be viewed by clicking the blue switch under the step near the top left.</li>
                    <li>Click the pencil icon and view notes for each chunk to see what concerns were brought up during 
                        the original translation process and in the peer check step.</li>
                    <li>The SUN dictionary, translation notes (tN) and words (tW) are available in the help tab. 
                        Frequently asked questions (FAQ) are available in the dropdown menu under your username in the top right-hand corner. </li>
                    <li>When all changes and notes have been made, click \"Yes, I did\" and \"{step}\".</li>",

    "peer-review-l2_desc" => "<li><b>PURPOSE:</b> to check the target text for accuracy while maintaining the naturalness of the 
                        language and <b>ensuring accurate and literal common language terms for \"Father\" and \"Son\" are 
                        used when referring to God the Father and Jesus Christ.</b></li>
                    <li><b>LENGTH:</b> Spend about 30 minutes on this step.</li>
                    <li>While checking, keep in mind the difference between accuracy and preference.</li>
                    <li>The primary purpose is to check the accuracy of the translation in detail.</li>
                    <li>There are two tabs: The Source text tab is the source text for checker to review before checking.
                        <ol>
                            <li>The LEFT column is the source text. There will be green and red highlights in the text. 
                                Green is text added by the 1st checker. Red is text deleted by the 1st checker. 
                                If there are no green or red highlights the 1st checker did not make changes to the text.</li>
                            <li>The RIGHT column is editable target text. Any changes made on this step by the checker will 
                                be reflected in the next checking steps:
                                <ul>
                                    <li>Added text will show in green.</li>
                                    <li>Deleted text will show in red.</li>
                                </ul>
                            </li>
                        </ol>
                    </li>
                    <li>If you see errors in spelling, punctuation, flow/naturalness, please correct it.</li>
                    <li>Check notes for each chunk to see what questions or corrections were made from the translation process (Level 1).
                        <ol>
                            <li>Notes are marked by a number next to the (<span class='mdi mdi-lead-pencil'></span>) icon.</li>
                            <li>Notes are viewed per \"chunk\", because they are carried from the translation process, which is done in chunks rather than verses.</li>
                            <li>If you have questions or thoughts for the next checkers, you can leave a note for the chunk.</li>
                        </ol>
                    </li>
                    <li>Any resources can be used for checking.</li>
                    <li>To add or edit a footnote for this chapter:
                        <ol>
                            <li>To add a footnote, click in the translated text where you want to add the footnote.</li>
                            <li>Click the footnote icon ( <i class='mdi mdi-bookmark'></i> ) on the right side of the translated text.</li>
                            <li>Click on \"ft\" to add footnote text.</li>
                            <li>To add an alternate text, click \"ft\" to add the text that explains the footnote, 
                                then click \"fqa\" to add the alternate text.</li>
                            <li>Click \"Save\" when text is added.</li>
                            <li>You will see your added footnote in blue highlight inserted in the translated text.</li>
                            <li>To edit the footnote, click on the footnote you want to edit and click the footnote 
                                icon and it will pop up the window for you edit the footnote.</li>
                            <li>To delete a footnote, click the \"x\" next to it. Click \"Save\" to save your changes.</li>
                        </ol>
                    </li>
                    <li>When all changes and notes have been made, click \"Yes, I did\" and \"{step}\".</li>",

    "peer-review-l2_chk_desc" => "<li><b>PURPOSE:</b> to check the target text for accuracy while maintaining the naturalness of the 
                        language and <b>ensuring accurate and literal common language terms for \"Father\" and \"Son\" are 
                        used when referring to God the Father and Jesus Christ.</b></li>
                    <li><b>LENGTH:</b> Spend about 30 minutes on this step.</li>
                    <li>While checking, keep in mind the difference between accuracy and preference.</li>
                    <li>The primary purpose is to check the accuracy of the translation in detail.</li>
                    <li>There are two tabs: The Source text tab is the source text for checker to review before checking.
                        <ol>
                            <li>The LEFT column is the source text. There will be green and red highlights in the text. 
                                Green is text added by the 1st checker. Red is text deleted by the 1st checker. 
                                If there are no green or red highlights the 1st checker did not make changes to the text.</li>
                            <li>The RIGHT column is editable target text. Any changes made on this step by the checker will 
                                be reflected in the next checking steps:
                                <ul>
                                    <li>Added text will show in green.</li>
                                    <li>Deleted text will show in red.</li>
                                </ul>
                            </li>
                        </ol>
                    </li>
                    <li>If you see errors in spelling, punctuation, flow/naturalness, please correct it.</li>
                    <li>Check notes for each chunk to see what questions or corrections were made from the translation process (Level 1).
                        <ol>
                            <li>Notes are marked by a number next to the (<span class='mdi mdi-lead-pencil'></span>) icon.</li>
                            <li>Notes are viewed per \"chunk\", because they are carried from the translation process, which is done in chunks rather than verses.</li>
                            <li>If you have questions or thoughts for the next checkers, you can leave a note for the chunk.</li>
                        </ol>
                    </li>
                    <li>Any resources can be used for checking.</li>
                    <li>To add or edit a footnote for this chapter:
                        <ol>
                            <li>To add a footnote, click in the translated text where you want to add the footnote.</li>
                            <li>Click the footnote icon ( <i class='mdi mdi-bookmark'></i> ) on the right side of the translated text.</li>
                            <li>Click on \"ft\" to add footnote text.</li>
                            <li>To add an alternate text, click \"ft\" to add the text that explains the footnote, 
                                then click \"fqa\" to add the alternate text.</li>
                            <li>Click \"Save\" when text is added.</li>
                            <li>You will see your added footnote in blue highlight inserted in the translated text.</li>
                            <li>To edit the footnote, click on the footnote you want to edit and click the footnote 
                                icon and it will pop up the window for you edit the footnote.</li>
                            <li>To delete a footnote, click the \"x\" next to it. Click \"Save\" to save your changes.</li>
                        </ol>
                    </li>
                    <li>When all changes and notes have been made, click \"Yes, I did\" and \"{step}\".</li>",

    "peer-review-l2_sun_desc" => "<li><b>PURPOSE:</b> to check the SUN translation for accuracy and consistency.</li>
                <li>While checking, make sure that common phrases are being translated the same way.</li>
                <li>Look that the correct symbol is being used.</li>
                <li>Look that all the SUN Grammar Rules are being followed.</li>
                <li>Look for punctuation, flow/naturalness errors.</li>
                <li>Look for missing portions of text or missing or added facts.</li>
                <li>Keep in mind the difference between accuracy and preference.</li>
                <li>Both SUN symbols and backSUN translation can be viewed by clicking the blue switch under the step near the top left.</li>
                <li>Click the pencil icon and view notes for each chunk to see what concerns were brought up during the original translation process.</li>
                <li>You can also leave your own notes in this section for the theological checker that follows you.</li>
                <li>The SUN dictionary, translation notes (tN) and words (tW) are available in the help tab. 
                    Frequently asked questions (FAQ) are available in the dropdown menu under your username in the top right-hand corner.</li>
                <li>When all changes and notes have been made, click \"Yes I did\" and \"{step}\"</li>",

    "keyword-check-l2_desc" => "<li><b>PURPOSE:</b> to ensure significant words are present in the translated text and accurately 
                                expressed, as well as <b>ensuring accurate and literal common language terms for \"Father\" and \"Son\" 
                                are used when referring to God the Father and Jesus Christ.</b></li>
                            <li><b>LENGTH:</b> Spend about 10 minutes on this step.</li>
                            <li><b>The point is not word for word translation, it is to see if the word/meaning is 
                                represented in the text in a way that is grammatically and culturally appropriate in the target language.</b></li>
                            <li>This step will be faster if you use a mouse instead of a touchpad.</li>
                            <li>The LEFT column is the source text with yellow highlighted words.
                                <ol>
                                    <li>The checker should click on each yellow highlighted word.</li>
                                    <li>A message will pop up that says, \"Click 'Yes' if this keyword is in target text 
                                        and accurate. Otherwise click 'No'. The keyword is: _____.</li>
                                    <li>If the keyword is represented correctly click 'YES' and the highlighting will disappear.</li>
                                    <li>If the keyword is not represented correctly click 'No' and the highlighting will stay. 
                                        Click the note icon (<span class='mdi mdi-lead-pencil'></span>) next 
                                        to the chunk and add a note regarding the incorrect word and click save.</li>
                                </ol>
                            </li>
                            <li>The RIGHT column is target text and cannot be edited. Any changes to keywords should 
                                be added to the notes by clicking the note icon 
                                (<span class='mdi mdi-lead-pencil'></span>) next to the chunk and add a note.</li>
                            <li>Any resources can be used.	</li>
                            <li>If there are additional thoughts or questions about the translation, click the note 
                                icon (<span class='mdi mdi-lead-pencil'></span>) next to the chunk and add the note and click save.</li>
                            <li>When all highlighted words have been checked, click \"Yes, I did\" and \"{step}\".</li>",

    "keyword-check-l2_chk_desc" => "<li><b>PURPOSE:</b> to ensure significant words are present in the translated text and accurately 
                                expressed, as well as <b>ensuring accurate and literal common language terms for \"Father\" and \"Son\" 
                                are used when referring to God the Father and Jesus Christ.</b></li>
                            <li><b>LENGTH:</b> Spend about 10 minutes on this step.</li>
                            <li><b>The point is not word for word translation, it is to see if the word/meaning is 
                                represented in the text in a way that is grammatically and culturally appropriate in the target language.</b></li>
                            <li>This step will be faster if you use a mouse instead of a touchpad.</li>
                            <li>The LEFT column is the source text with yellow highlighted words.
                                <ol>
                                    <li>The checker should click on each yellow highlighted word.</li>
                                    <li>A message will pop up that says, \"Click 'Yes' if this keyword is in target text 
                                        and accurate. Otherwise click 'No'. The keyword is: _____.</li>
                                    <li>If the keyword is represented correctly click 'YES' and the highlighting will disappear.</li>
                                    <li>If the keyword is not represented correctly click 'No' and the highlighting will stay. 
                                        Click the note icon (<span class='mdi mdi-lead-pencil'></span>) next 
                                        to the chunk and add a note regarding the incorrect word and click save.</li>
                                </ol>
                            </li>
                            <li>The RIGHT column is target text and cannot be edited. Any changes to keywords should 
                                be added to the notes by clicking the note icon 
                                (<span class='mdi mdi-lead-pencil'></span>) next to the chunk and add a note.</li>
                            <li>Any resources can be used.	</li>
                            <li>If there are additional thoughts or questions about the translation, click the note 
                                icon (<span class='mdi mdi-lead-pencil'></span>) next to the chunk and add the note and click save.</li>
                            <li>When all highlighted words have been checked, click \"Yes, I did\" and \"{step}\".</li>",

    "content-review-l2_desc" => "<li><b>PURPOSE:</b> to check the target text for accuracy and discuss discrepancies with a peer 
                            checker while <b>ensuring accurate and literal common language terms for \"Father\" and \"Son\" 
                            are used when referring to God the Father and Jesus Christ.</b></li>
                        <li><b>LENGTH:</b> Spend about 30 minutes on this step.</li>
                        <li><b>This is the final check; all changes should be made before you complete this step.</b></li>
                        <li>While checking, keep in mind the difference between accuracy and preference.</li>
                        <li>After you have looked over the text, contact the peer-checker to discuss discrepancies via: 
                            Skype, Messenger, WhatsApp, phone, etc.</li>
                        <li>The Source text tab has text with yellow highlighted words.
                            <ol>
                                <li>Click on each yellow highlighted word that is remaining and discuss with the 
                                    peer-checker if the word in the target text is accurate.</li>
                                <li>A message will pop up that says, \"Click 'Yes' if this keyword is in target text 
                                    and accurate. Otherwise click 'No'. The keyword is: _____.</li>
                                <li>If the keyword is represented correctly click 'YES' and the highlighting will disappear.</li>
                                <li>If the keyword is not represented correctly, make appropriate changes to the target text. 
                                    Do not forget to check the notes from previous translators and checkers for discussion on this chunk.</li>
                                <li><b>All highlights have to be resolved on this step. You cannot complete this step 
                                    until all highlights are removed.</b></li>
                            </ol>
                        </li>
                        <li>The Target text tab:
                            <ol>
                                <li>The LEFT column is the source text. There will be green and red highlights in the text. 
                                    Green is text added by the checkers. Red is text deleted by the checkers. If there 
                                    are no green or red highlights the checkers did not make changes to the text.</li>
                                <li>The RIGHT column is editable (the peer-checker cannot edit, but they can see 
                                    all changes made as they are made by refreshing their screen).</li>
                            </ol>
                        </li>
                        <li>You and the peer-checker should discuss any changes you think should be made. 
                            If you agree to the changes, you should make the changes right away. If you do not agree, 
                            contact your facilitator to discuss the disagreement. DO NOT complete this step until 
                            discrepancies are resolved and changed.</li>
                        <li>Any resources can be used for checking.</li>
                        <li>To add or edit a footnote for this chapter:
                            <ol>
                                <li>To add a footnote, click in the translated text where you want to add the footnote.</li>
                                <li>Click the footnote icon ( <i class='mdi mdi-bookmark'></i> ) on the right side of the translated text.</li>
                                <li>Click on \"ft\" to add footnote text.</li>
                                <li>To add an alternate text, click \"ft\" to add the text that explains the footnote, 
                                    then click \"fqa\" to add the alternate text.</li>
                                <li>Click \"Save\" when text is added.</li>
                                <li>You will see your added footnote in blue highlight inserted in the translated text.</li>
                                <li>To edit the footnote, click on the footnote you want to edit and click the footnote 
                                    icon and it will pop up the window for you edit the footnote.</li>
                                <li>To delete a footnote, click the \"x\" next to it. Click \"Save\" to save your changes.</li>
                            </ol>
                        </li>
                        <li>When all changes and notes have been made, click \"Yes, I did\" and \"{step}\".</li>",

    "content-review-l2_chk_desc" => "<li><b>PURPOSE:</b> to check the target text for accuracy and discuss discrepancies with 
                                a peer checker while <b>ensuring accurate and literal common language terms for \"Father\" and \"Son\" 
                                are used when referring to God the Father and Jesus Christ.</b></li>
                            <li><b>LENGTH:</b> Spend about 30 minutes on this step.</li>
                            <li><b>This is the final check, so all changes should be made before you complete this step.</b></li>
                            <li>After you have looked over the text, contact the peer-checker to discuss discrepancies 
                                via: Skype, Messenger, WhatsApp, phone, etc…</li>
                            <li>Your peer-checker will make all edits to the target text.  You can see all edits 
                                by clicking refresh on your browser as they make changes.</li>
                            <li>The Source text tab has text with yellow highlighted words.
                                <ol>
                                    <li>Discuss with the peer-checker if the word in the target text is accurate.</li>
                                    <li>If the keyword is represented correctly the peer-checker will click 'YES' 
                                        on a pop-up message on their page, and the highlighting will disappear.</li>
                                    <li>If the keyword is not represented correctly, the peer-checker will make 
                                        appropriate changes to the target text. Do not forget to check the notes from 
                                        previous translators and checkers for discussion on this chunk.</li>
                                    <li><b>All highlights have to be resolved on this step. You cannot complete this step 
                                        until all highlights are removed.</b></li>
                                </ol>
                            </li>
                            <li>The Target text tab:
                                <ol>
                                    <li>The LEFT column is the source text. There will be green and red highlights in the text. 
                                        Green is text added by the checkers. Red is text deleted by the checkers. 
                                        If there are no green or red highlights the checkers did not make changes to the text.</li>
                                    <li>The RIGHT column <b>can be edited by your peer-checker</b> (again, you can see all 
                                        changes made as they are made by refreshing your screen).</li>
                                </ol>
                            </li>
                            <li>While checking, keep in mind the difference between accuracy and preference.</li>
                            <li>You and the peer-checker should discuss any changes you think should be made. If you 
                                agree to the changes, your peer-checker will make the changes right away. If you do 
                                not agree, contact your facilitator to discuss the disagreement. DO NOT complete 
                                this step until discrepancies are resolved and changed.</li>
                            <li>Any resources can be used for checking.</li>
                            <li>Footnotes added to the translation are inserted in blue highlighting.
                                <ol>
                                    <li>Footnotes are marked with the tags: \"\\f\", \"\\ft\" and \"\\fqa . \"</li>
                                    <li>\"\\f\" tags the beginning and end of all footnotes.</li>
                                    <li>\"\\f + \\ft\" tags the beginning of a standard footnote.</li>
                                    <li>\"\\fqa\" tags the start of a footnote indicating an alternate translation of scripture. </li>
                                    <li>Check to see if the footnote is relevant and correct.</li>
                                </ol>
                            </li>
                            <li>When all changes and notes have been made, click \"Yes, I did\" and \"{step}\".</li>",

    "multi-draft" => "Черновик",
    "multi-draft_full" => "Чтение, Пересказ и Черновик",

    "multi-draft_tq_desc" => "<li><b>PURPOSE:</b> to read the source text, to say it aloud to engage your memory, and 
                                    to draft the text in the target language.</li>
                                <li>Read the assigned question and answer carefully in the source text.</li>
                                <li>Click on the “Consume” box to indicate this step is completed.</li>
                                <li>Say the question and answer aloud to engage your memory in the process.</li>
                                <li>Click on the \"Verbalize”\" box to indicate this step is completed. This will \"unlock\" the text box.</li>
                                <li>Translate the question and answer in the text box.
                                    <ol>
                                        <li>As much as possible, translate phrase by phrase and not word for word, to preserve naturalness of the target language.</li>
                                        <li>Some verses have more than one question</li>
                                        <li>The question is the larger text. To format double click and select \"Header 1.\"</li>
                                        <li>The answer is the smaller text. The format for the answer should not need to be changed. It is the \"normal\" font.</li>
                                    </ol>
                                </li>
                                <li>When you finish translating that verse’s question(s) and answer(s), click on 
                                    the \"Draft\" box to indicate this step is completed. You can go back and edit at any time, if needed.</li>
                                <li>Go to the next verse and start the process again with \"Consume,\" then \"Verbalize,\" 
                                    and \"Draft\" until all questions are translated for the chapter.</li>
                                <li>Click \"Yes I did\" and \"{step}\" to move to the next step.</li>",

    "self-check_tq_desc" => "<li><b>PURPOSE:</b> to edit your translated Questions/Answers and check accuracy while maintaining naturalness.</li>
                        <li>Look for spelling, punctuation, flow/naturalness errors.</li>
                        <li>Look for any missing portions of text or missing or added facts.</li>
                        <li>Check the accuracy of your translation in detail.</li>
                        <li>If you have additional thoughts or questions about your translation, click the note 
                            icon (<span class='mdi mdi-lead-pencil'></span>) next to the chunk and add the note and 
                            click save. The checkers will see this note and help resolve the question or problem.</li>
                        <li>When all changes and notes have been made, click \"Yes I did\" and \"{step}.\" </li>",

    "keyword-check_tq_desc" => "<li><b>PURPOSE:</b> to ensure significant words are present in the translated Questions/Answers and accurately expressed.</li>
                        <li>The checker will look for Keywords in the Questions and Answers and compare them to the 
                            translated Questions and Answers to ensure the meaning is represented properly.</li>
                        <li>Keywords are: proper names (people/cities), numbers, pronouns, and any important terms.</li>
                        <li><b>The point is not word for word translation, it is to see if the word/meaning is represented 
                            in the text in a way that is grammatically and culturally appropriate in the target language.</b></li>
                        <li>When the checker has finished checking the text, they will contact you to discuss keyword 
                            and recommended changes via: Skype, Messenger, WhatsApp, phone, etc…</li>
                        <li>The checker’s comments on corrections will be in the notes which you can see by clicking the 
                            note icon (<span class='mdi mdi-lead-pencil'></span>) next to the chunk.</li>
                        <li>You and the checker should discuss any changes they think should be made. 
                            If you agree to the changes, the translator should make the changes right away. 
                            If you disagree, do not waste a lot of time discussing.  Leave the text as it is and 
                            make a note on the chunk. It will be resolved in the next step.</li>
                        <li>When all changes and notes have been made, the checker will click, \"Yes I did\" and \"{step},\" 
                            then you should click \"Yes I did\" and \"{step},\" to move to the next step.</li>",

    "keyword-check_tq_chk_desc" => "<li><b>PURPOSE:</b> to ensure significant words are present in the translated Questions/Answers and accurately expressed.</li>
                        <li>As the checker your screen will say \"checking mode\" and instructions will be in orange.</li>
                        <li>You will look for Keywords in the Questions and Answers and compare them to the 
                            translated Questions and Answers to ensure the meaning is represented properly.</li>
                        <li>If you think changes should be made, click the note 
                            icon (<span class='mdi mdi-lead-pencil'></span>) next to the chunk and add the note and click save.</li>
                        <li>The keywords are: proper names (people/cities), numbers, pronouns, and any important.</li>
                        <li><b>The point is not word for word translation, it is to see if the word/meaning is 
                            represented in the text in a way that is grammatically and culturally appropriate in the target language.</b></li>
                        <li>When you finish checking the text, contact the translator to discuss keyword and 
                            recommended changes via: Skype, Messenger, WhatsApp, phone, etc…</li>
                        <li>You and the translator should discuss any changes you think should be made. 
                            If you agree to the changes, the translator should make the changes right away. 
                            If you disagree, do not waste a lot of time discussing.  Leave the text as it is and make 
                            a note on the chunk noting the disagreement. It will be resolved in the next step.</li>
                        <li>When all changes and notes have been made click, \"Yes I did\" and \"{step}.\" 
                            Do not simply close the page by clicking the \"x\" or the translator will not be able to continue to the next step.</li>",

    "peer-review_sun" => "Theological Check",
    "peer-review_tq" => "Обзор пастора",
    "peer-review_tw" => "Обзор пастора",
    "peer-review_obs" => "Обзор пастора",

    "peer-review_tq_desc" => "<li><b>PURPOSE:</b> to review the Questions/Answers and check accuracy while maintaining naturalness.</li>
                        <li>The reviewer will look for spelling, punctuation, flow/naturalness errors, as well as, 
                            missing portions of text or missing or added facts.</li>
                        <li><b>The point is not word for word translation, it is to see if the word/meaning is represented 
                            in the text in a way that is grammatically and culturally appropriate in the target language.</b></li>
                        <li>When the reviewer has finished checking the text, they will contact you to 
                            discuss recommended changes via: Skype, Messenger, WhatsApp, phone, etc…</li>
                        <li>The reviewer’s comments on corrections will be in the notes which you can see by 
                            clicking the note icon (<span class='mdi mdi-lead-pencil'></span>) next to the chunk.</li>
                        <li>You should discuss any changes they think should be made.  If you agree to the changes, you should 
                            make the changes right away.  If you disagree, discuss the issue with your facilitator 
                            and resolve before you complete this step.</li>
                        <li>When all changes have been made, the checker will click, \"Yes I did\" and \"{step},\" 
                            then you should click \"Yes I did\" and \"{step},\" to complete the step.</li>
                        <li><b>Make sure all changes are made as this is the last step.</b></li>",

    "peer-review_tq_chk_desc" => "<li><b>PURPOSE:</b> to review the Questions/Answers and check accuracy while maintaining naturalness.</li>
                        <li>Look for spelling, punctuation, flow/naturalness errors.</li>
                        <li>Look for any missing portions of text or missing or added facts.</li>
                        <li>Check the accuracy of translation in detail.</li>
                        <li>Make sure to look at previous translator and checker’s notes on the chunk for any questions or unresolved problems.</li>
                        <li><b>The point is not word for word translation, it is to see if the word/meaning is represented 
                            in the text in a way that is grammatically and culturally appropriate in the target language.</b></li>
                        <li>When you have finished checking the text, contact the translator to discuss recommended 
                            changes via: Skype, Messenger, WhatsApp, phone, etc…</li>
                        <li>You should discuss any changes you think should be made.  If you agree to the changes, the 
                            translator should make the changes right away.  If you disagree, discuss the issue with your 
                            facilitator and resolve before you complete this step.</li>
                        <li>When all changes have been made click, \"Yes I did\" and \"{step}.\" Do not simply close the 
                            page by clicking the \"x\" or the translator will not be able to complete the step.</li>
                        <li><b>Make sure all changes are made as this is the last step.</b></li>",

    "multi-draft_tw_desc" => "<li><b>PURPOSE:</b> o read the source text, to say it aloud to engage your memory, and 
                                    draft the text in the target language.</li>
                                <li>Read the assigned Words, Definitions/Facts & Translation Suggestions carefully in the source text.</li>
                                <li>Click on the \"Consume\" box to indicate this step is completed.</li>
                                <li>Say the Words, Definitions/Facts & Translation Suggestions aloud to engage your memory in the process.</li>
                                <li>Click on the \"Verbalize\" box to indicate this step is completed. This will \"unlock\" the text box.</li>
                                <li>Translate the Words, Definitions/Facts & Translation Suggestions in the text box.
                                    <ol>
                                        <li>As much as possible, translate phrase by phrase and not word for word, to preserve 
                                            naturalness of the target language when translating the Definitions/Facts & Translation Suggestions.</li>
                                        <li>All formatting is done by double clicking the word or any word in the line. 
                                            The formatting box will pop up for you to choose font size or bullet points.</li>
                                        <li>To format Translation Word, double click the word and select \"Header 1\".</li>
                                        <li>To format subtitles, such as \"Facts\", \"Definitions\", \"Translation Suggestions\"; double click and select \"Header 2\".</li>
                                        <li>You should not need to change the format of the content. It is the \"normal\" font.</li>
                                    </ol>
                                </li>
                                <li>You do not need to translate the following sections: Bible References, Examples from the Bible Stories, or Word Data.</li>
                                <li>When you finish translating, click on the \"Draft\" box to indicate this step is completed. 
                                    You can go back and edit at any time, if needed.</li>
                                <li>Go to the next Word and start the process again with \"Consume\", then \"Verbalize\", and \"Draft\" until all Words are translated for the chapter.</li>
                                <li>Click \"Yes I did\" and \"{step}\" to move to the next step.</li>",

    "self-check_tw_desc" => "<li><b>PURPOSE:</b> to edit your translated Words and check accuracy while maintaining naturalness.</li>
                        <li>Look for spelling, punctuation, flow/naturalness errors.</li>
                        <li>Look for any missing portions of text or missing or added facts.</li>
                        <li>Check the accuracy of your translation in detail.</li>
                        <li>If you have additional thoughts or questions about your translation, click the note 
                            icon <br>(<span class='mdi mdi-lead-pencil'></span>) next to the chunk and add the note and 
                            click save. The checkers will see this note and help resolve the question or problem.</li>
                        <li>When all changes and notes have been made, click \"Yes I did\" and \"{step}.\" </li>",

    "keyword-check_tw_desc" => "<li><b>PURPOSE:</b> to ensure significant words are present in the translated Words and accurately expressed.</li>
                        <li>The checker will look for Keywords and compare them to the 
                            translated Words to ensure the meaning is represented properly.</li>
                        <li>Keywords are: proper names (people/cities), numbers, pronouns, and any important terms.</li>
                        <li><b>The point is not word for word translation, it is to see if the word/meaning is represented 
                            in the text in a way that is grammatically and culturally appropriate in the target language.</b></li>
                        <li>When the checker has finished checking the text, they will contact you to discuss keyword 
                            and recommended changes via: Skype, Messenger, WhatsApp, phone, etc…</li>
                        <li>The checker’s comments on corrections will be in the notes which you can see by clicking the 
                            note icon (<span class='mdi mdi-lead-pencil'></span>) next to the chunk.</li>
                        <li>You and the checker should discuss any changes they think should be made. 
                            If you agree to the changes, the translator should make the changes right away. 
                            If you disagree, do not waste a lot of time discussing.  Leave the text as it is and 
                            make a note on the chunk. It will be resolved in the next step.</li>
                        <li>When all changes and notes have been made, the checker will click, \"Yes I did\" and \"{step}\", 
                            then you should click \"Yes I did\" and \"{step}\", to move to the next step.</li>",

    "keyword-check_tw_chk_desc" => "<li><b>PURPOSE:</b> to ensure significant words are present in the translated Words and accurately expressed.</li>
                        <li>As the checker your screen will say \"checking mode\" and instructions will be in orange.</li>
                        <li>You will look for keywords and compare them to the 
                            translated Words to ensure the meaning is represented properly.</li>
                        <li>If you think changes should be made, click the note 
                            icon (<span class='mdi mdi-lead-pencil'></span>) next to the chunk and add the note and click save.</li>
                        <li>The keywords are: proper names (people/cities), numbers, pronouns, and any important.</li>
                        <li><b>The point is not word for word translation, it is to see if the word/meaning is 
                            represented in the text in a way that is grammatically and culturally appropriate in the target language.</b></li>
                        <li>When you finish checking the text, contact the translator to discuss keyword and 
                            recommended changes via: Skype, Messenger, WhatsApp, phone, etc…</li>
                        <li>You and the translator should discuss any changes you think should be made. 
                            If you agree to the changes, the translator should make the changes right away. 
                            If you disagree, do not waste a lot of time discussing.  Leave the text as it is and make 
                            a note on the chunk noting the disagreement. It will be resolved in the next step.</li>
                        <li>When all changes and notes have been made click, \"Yes I did\" and \"{step}.\" 
                            Do not simply close the page by clicking the \"x\" or the translator will not be able to continue to the next step.</li>",

    "peer-review_tw_desc" => "<li><b>PURPOSE:</b> to review the Words and check accuracy while maintaining naturalness.</li>
                        <li>The reviewer will look for spelling, punctuation, flow/naturalness errors, as well as, 
                            missing portions of text or missing or added facts.</li>
                        <li><b>The point is not word for word translation, it is to see if the word/meaning is represented 
                            in the text in a way that is grammatically and culturally appropriate in the target language.</b></li>
                        <li>When the reviewer has finished checking the text, they will contact you to 
                            discuss recommended changes via: Skype, Messenger, WhatsApp, phone, etc…</li>
                        <li>The reviewer’s comments on corrections will be in the notes which you can see by 
                            clicking the note icon (<span class='mdi mdi-lead-pencil'></span>) next to the chunk.</li>
                        <li>You should discuss any changes they think should be made.  If you agree to the changes, you should 
                            make the changes right away.  If you disagree, discuss the issue with your facilitator 
                            and resolve before you complete this step.</li>
                        <li>When all changes have been made, the checker will click, \"Yes I did\" and \"{step},\" 
                            then you should click \"Yes I did\" and \"{step},\" to complete the step.</li>
                        <li><b>Make sure all changes are made as this is the last step.</b></li>",

    "peer-review_tw_chk_desc" => "<li><b>PURPOSE:</b> to review the Words and check accuracy while maintaining naturalness.</li>
                        <li>Look for spelling, punctuation, flow/naturalness errors.</li>
                        <li>Look for any missing portions of text or missing or added facts.</li>
                        <li>Check the accuracy of translation in detail.</li>
                        <li>Make sure to look at previous translator and checker’s notes on the chunk for any questions or unresolved problems.</li>
                        <li><b>The point is not word for word translation, it is to see if the word/meaning is represented 
                            in the text in a way that is grammatically and culturally appropriate in the target language.</b></li>
                        <li>When you have finished checking the text, contact the translator to discuss recommended 
                            changes via: Skype, Messenger, WhatsApp, phone, etc…</li>
                        <li>You should discuss any changes you think should be made.  If you agree to the changes, the 
                            translator should make the changes right away.  If you disagree, discuss the issue with your 
                            facilitator and resolve before you complete this step.</li>
                        <li>When all changes have been made click, \"Yes I did\" and \"{step}.\" Do not simply close the 
                            page by clicking the \"x\" or the translator will not be able to complete the step.</li>
                        <li><b>Make sure all changes are made as this is the last step.</b></li>",


    // ------- Level 3 Check --------- //
    "peer-review-l3" => "Парная проверка",
    "peer-review-l3_full" => "Парная проверка",

    "peer-review-l3_tn_desc" => "<li><b>PURPOSE:</b> to update the scripture (to match Level 3) in the translated Notes and check for accuracy in the Notes while maintaining naturalness. </li>
                            <li>This step is done with a peer-checker.</li>
                            <li>While checking, keep in mind the difference between accuracy and preference.</li>
                            <li>The column on the left is the scripture source text showing changes between Level 2 and Level 3 checking.
                                <ol>
                                    <li>Text added in Level 3 check will be highlighted in green.</li>
                                    <li>Text deleted in Level 3 check will be highlighted in red/pink.</li>
                                    <li>To turn off \"Comparison mode\" and see translated text formatted and without green/red highlights switch \"Comparison mode\" to \"Off.\"</li>
                                    <li>It can be switched on or off as needed while checking.</li>
                                    <li>Introductions (Intro) will not have scripture to compare in the left column.</li>
                                </ol>
                            </li>
                            <li>The column on the right is the translated Notes.</li>
                            <li>The source Notes in English are in the sidebar. Click the tN icon to view them.</li>
                            <li>Check the accuracy of the scripture in the Note in detail (the largest text)! <b>It should be exactly the same as the scripture source text in the left column.</b></li>
                            <li>Check to ensure the note for that scripture is accurate.</li>
                            <li>Check errors in spelling, punctuation, and flow/naturalness.</li>
                            <li>Check the notes for each chunk to see what questions or corrections were made in previous steps.
                                <ol>
                                    <li>Notes are found in the top right of the chunk marked by the (<span class='mdi mdi-lead-pencil'></span>) icon and a number indicating how many notes were added for that chunk by previous translators/checkers.</li>
                                    <li>If there are changes to be made to the translated Notes, click on the (<span class='mdi mdi-lead-pencil'></span>) icon and leave a note.</li>
                                    <li>Your peer-checker will also be able to see your notes by clicking \"refresh.\"</li>
                                </ol>
                            </li>
                            <li>When all changes have been noted, click \"Yes, I did\" and \"{step}\" and contact your peer-checker to start the 2nd step.</li>",

    "peer-review-l3_desc" => "<li><b>PURPOSE:</b> to check the target text for accuracy while maintaining the naturalness of the language and <b>ensuring accurate and literal common language terms for \"Father\" and \"Son\" are used when referring to God the Father and Jesus Christ.</b></li>
                            <li>This step is done with a peer-checker.</li>
                            <li>Before you start, please review the QA Guide for your translation by clicking the orange QA Guide icon on the sidebar.</li>
                            <li>While checking, keep in mind the difference between accuracy and preference.</li>
                            <li>The column on the left is the source text, and the right is target text.</li>
                            <li>Check the accuracy of the translation in detail.</li>
                            <li>Check errors in spelling, punctuation, and flow/naturalness.</li>
                            <li>Check the notes for each chunk to see what questions or corrections were made from the translation process (Level 1) and checking process (Level 2).
                                <ol>
                                    <li>Notes are found in the top right of the chunk marked by the (<span class='mdi mdi-lead-pencil'></span>) icon and a number indicating how many notes were added for that chunk by previous translators/checkers.</li>
                                    <li>If there are changes to be made to the target text, click on the (<span class='mdi mdi-lead-pencil'></span>) icon and leave a note.</li>
                                    <li>Your peer-checker will also be able to see your notes by clicking \"refresh.\"</li>
                                </ol>
                            </li>
                            <li>The following resources can be used.
                                <ol>
                                    <li>Translation Notes in the sidebar. To access the notes, click the tN icon.</li>
                                    <li>Translation Words in the sidebar. To access the words, click the tW icon.</li>
                                    <li>Translation Questions in the sidebar. To access the questions, click the tQ icon.</li>
                                    <li>Any other resources you choose.</li>
                                </ol>
                            </li>
                            <li>When all changes have been noted, click \"Yes, I did\" and \"{step}\" and contact your peer-checker to start the 2nd step.</li>",


    "peer-edit-l3" => "Парный обзор",
    "peer-edit-l3_full" => "Парный обзор",

    "peer-edit-l3_tn_desc" => "<li><b>PURPOSE:</b> to update the scripture (to match Level 3) in the translated Notes and check for accuracy in the Notes while maintaining naturalness. </li>
                            <li>To begin this step, contact your peer-checker from step 1 via Skype, Messenger, WhatsApp, phone, etc.</li>
                            <li>Together you will discuss changes you noted in step 1 you thought should be made to target text.</li>
                            <li>You can review your peer’s notes and all other notes from previous translators/checkers by clicking the notes icon (<span class='mdi mdi-lead-pencil'></span>).</li>
                            <li>Discuss the accuracy of the translation Notes.</li>
                            <li>Discuss any errors in spelling, punctuation, and flow/naturalness.</li>
                            <li>The source Notes in English are in the sidebar. Click the tN icon to view them.</li>
                            <li>Check the accuracy of the scripture in the Note in detail (the largest text)! <b>It should be exactly the same as the scripture source text in the left column.</b></li>
                            <li>Make changes to the Notes as you discuss the changes that should be made.  If you disagree on a change consult your facilitator or language leader. </li>
                            <li>Make changes to the formatting, as needed.
                                <ol>
                                    <li>Click the format icon (<i class='note-icon-magic'></i>) and select the size text for that line.</li>
                                    <li>Scripture select:  Header 1.</li>
                                    <li>Translated Note select: Normal.</li>
                                    <li>For Book and Chapter Intro use the following format: Introduction – Header 1, Part – Header 2, Titles - Header 4, Notes – Normal.</li>
                                    <li>No need to translate links.</li>
                                </ol>
                            </li>
                            <li><b>Do not complete this step until all changes are made and disagreements are resolved!</b></li>
                            <li>When all changes have been noted, click \"Yes, I did\" and \"{step}\" and contact your peer-checker to start the 2nd step.</li>",

    "peer-edit-l3_tn_chk_desc" => "<li><b>PURPOSE:</b> to update the scripture (to match Level 3) in the translated Notes and check for accuracy in the Notes while maintaining naturalness. </li>
                            <li>To begin this step, contact your peer-checker from step 1 via Skype, Messenger, WhatsApp, phone, etc.</li>
                            <li>Together you will discuss changes you noted in step 1 you thought should be made to target text.</li>
                            <li>You can review your peer’s notes and all other notes from previous translators/checkers by clicking the notes icon (<span class='mdi mdi-lead-pencil'></span>).</li>
                            <li>Discuss the accuracy of the translation Notes.</li>
                            <li>Discuss any errors in spelling, punctuation, and flow/naturalness.</li>
                            <li>The source Notes in English are in the sidebar. Click the tN icon to view them.</li>
                            <li>Check the accuracy of the scripture in the Note in detail (the largest text)! <b>It should be exactly the same as the scripture source text in the left column.</b></li>
                            <li>Your peer will make changes to the Notes as you discuss the changes that should be made.  If you disagree on a change consult your facilitator or language leader. </li>
                            <li>Your peer will make changes to the formatting, as needed.</li>
                            <li><b>Do not complete this step until all changes are made and disagreements are resolved!</b></li>
                            <li>When all changes have been noted, click \"Yes, I did\" and \"{step}\" and contact your peer-checker to start the 2nd step.</li>",

    "peer-edit-l3_desc" => "<li><b>PURPOSE:</b> to check the target text for accuracy while maintaining the naturalness of the language and <b>ensuring accurate and literal common language terms for \"Father\" and \"Son\" are used when referring to God the Father and Jesus Christ.</b></li>
                            <li>To begin this step, contact your peer-checker from step 1 via Skype, Messenger, WhatsApp, phone, etc.</li>
                            <li>Together you will discuss changes you noted in step 1 you thought should be made to target text.</li>
                            <li>You can review your peer’s notes and all other notes from previous translators/checkers by clicking the notes icon (<span class='mdi mdi-lead-pencil'></span>).</li>
                            <li>Discuss the accuracy of the translation.</li>
                            <li>Discuss any errors in spelling, punctuation, and flow/naturalness.</li>
                            <li>Make changes to the target text as you discuss the changes that should be made. If you disagree on a change consult your facilitator or language leader. </li>
                            <li>The following resources can be used.
                                <ol>
                                    <li>Translation Notes in the sidebar. To access the notes, click the tN icon.</li>
                                    <li>Translation Words in the sidebar. To access the words, click the tW icon.</li>
                                    <li>Translation Questions in the sidebar. To access the questions, click the tQ icon.</li>
                                    <li>Any other resources you choose.</li>
                                </ol>
                            </li>
                            <li><b>Do not complete this step until all changes are made and disagreements are resolved!</b></li>
                            <li>When all changes have been noted, click \"Yes, I did\" and \"{step}\" and contact your peer-checker to start the 2nd step.</li>",

    "peer-edit-l3_chk_desc" => "<li><b>PURPOSE:</b> to check the target text for accuracy while maintaining the naturalness of the language and <b>ensuring accurate and literal common language terms for \"Father\" and \"Son\" are used when referring to God the Father and Jesus Christ.</b></li>
                            <li>To begin this step, contact your peer-checker from step 1 via Skype, Messenger, WhatsApp, phone, etc.</li>
                            <li>Together you will discuss changes you noted in step 1 you thought should be made to target text.</li>
                            <li>You can review your peer’s notes and all other notes from previous translators/checkers by clicking the notes icon (<span class='mdi mdi-lead-pencil'></span>).</li>
                            <li>Discuss the accuracy of the translation.</li>
                            <li>Discuss any errors in spelling, punctuation, and flow/naturalness.</li>
                            <li>Your peer will make changes to the target text as you discuss the changes that should be made. You can see all changes by clicking \"refresh\" in your browser. If you disagree on a change consult your facilitator or language leader.</li>
                            <li>The following resources can be used.
                                <ol>
                                    <li>Translation Notes in the sidebar. To access the notes, click the tN icon.</li>
                                    <li>Translation Words in the sidebar. To access the words, click the tW icon.</li>
                                    <li>Translation Questions in the sidebar. To access the questions, click the tQ icon.</li>
                                    <li>Any other resources you choose.</li>
                                </ol>
                            </li>
                            <li><b>Do not complete this step until all changes are made and disagreements are resolved!</b></li>
                            <li>When all changes have been noted, click \"Yes, I did\" and \"{step}\" and contact your peer-checker to start the 2nd step.</li>",

    // ------- Errors messages ------- //
    "checker_translator_not_ready_error" => "Переводчик не готов к этому шагу, пожалуйста ждите. Страница будет перезагружена, когда переводчик будет готов.<span class=\"checker_waits\"></span>",
    "checker_not_ready_error" => "Проверяющий ещё не утвердил этот шаг. Пожалуйста ждите.",
    "peer_checker_not_ready_error" => "Ваш напарник еще не перешел на этот шаг. Пожалуйста ждите.",
    "verb_checker_not_ready_error" => "Добавьте проверяющего, чтобы прейти к следующему шагу.",
    "checker_translator_finished_error" => "Проверка уже закончена для данной главы.",
    "empty_or_not_permitted_event_error" => "У вас недостаточно прав, чтобы просматривать информацию об этой книге.",
    "not_started_event_error" => "Перевод еще не начался. Вы можете перейти на <a href=\"/activities/manage/{0}\">страницу управления книгой</a> для распределения переводчиков и глав.",
    "partner_not_ready_error" => "Вы не можете перейти к следующему шагу, пока ваш партнер не готов к этому шагу.",
    "wrong_event_state_error" => "Перевод книги еще не начался. Пожалуйста ждите, пока фасилитатор не допустит вас.",
    "not_possible_to_save_error" => "Вы не можете редактировать перевод после того, как проверяющий утвердил его.",
    "empty_verses_error" => "Вы перевели не все отрывки.",
    "wrong_chunks_error" => "Неверно усстановлены отрывки.",
    "error_ocured" => "Упс! Произошла ошибка. {0}",
    "error_member_in_event" => "Участник уже подал заявку на участие в этой книге. Возможно на предыдущих уровнях.",
    "no_l2_3_checkers_available_error" => "Регистрация проверяющих {0}-го уровня закрыта.",
    "no_translators_available_error" => "Регистрация переводчиков закрыта.",
    "required_fields_empty_error" => "Вы не правильно заполнили обязательные поля.",
    "not_loggedin_error" => "Вы не авторизованы.",
    "account_not_verirfied_error" => "Ваша учетная запись не подтверждена. Обратитесь к администрации сайта.",
    "event_notexist_error" => "Книга не существует.",
    "no_source_error" => "Текст исходного перевода не найден.",
    "not_enough_rights_error" => "Недостаточно прав для выполнения этого действия!",
    "not_enough_lang_rights_error" => "Недостаточно прав для создания исходника этого языка.",
    "refresh_resource_error" => "Не удалось обновить исходник. Возможно исходник не доступен.",
    "event_translating_error" => "Вы не можете удалить эту главу, так как в ней уже имеются переведенные стихи.",
    "chapter_aready_assigned_error" => "Глава назначена другому переводчику!",
    "chapter_checker_used_error" => "Глава была проверена этим проверяющим!",
    "event_chapters_error" => "Необходимо назначить хотя бы одну главу, чтобы начать перевод.",
    "peer_check_not_done_error" => "Ваша предыдущая глава не была проверена вашим партнёром.",
    "cannot_apply_checker" => "Возникла ошибка. Вы не можете стать проверяющим этой главы.",
    "enter_admins" => "Назначьте хотя бы одного фасилитатора для этой книги",
    "empty_draft_verses_error" => "Вы перевели не все стихи",
    "empty_words_error" => "Ошибка: поле не заполнено",
    "not_in_event_error" => "Вы не учавствуете в переводе этой книги.",
    "checker_event_error" => "Вы не являетесь проверяющим для этого переводчика",
    "not_equal_verse_markers" => "Вы не верно расставили маркеры стихов",
    "translator_has_chapter" => "Переводчики/проверяющие, которым назначены главы, не могут быть удалены из книги.",
    "event_already_exists" => "Это книга уже была создана",
    "event_not_exists_error" => "Эта книга еще не создана",
    "gateway_language_exists_error" => "Проект этого ключевого языка уже был создан",
    "not_allowed_action" => "Произошла ошибка. Пожалуйста, попробуйте еще раз.",
    "wrong_parameters" => "Неверные параметры. Пожалуйста, попробуйте еще раз.",
    "event_is_finished" => "Книга завершена. Действие невозможно.",
    "keywords_still_exist_error" => "Вы не можете утвердить эту главу, пока в ней присутствуют ключевые слова.",
    "keywords_empty_error" => "Вы должны выделить проблематичные слова.",
    "usfm_not_valid_error" => "Проект не действителен. Убедитесь, что проект содержит все главы, отрывки и стихи.",
    "event_has_translations_error" => "Вы не можете импортировать поверх существующего перевода. Либо удалите событие, либо дождитесь его окончания, если оно находится на стадии перевода/проверки.",
    "event_does_not_exist_error" => "Книга не существует. Необходимо создать её для того, чтобы можно было импортировать перевод.",
    "unknown_import_type_error" => "Неизвестный тип проекта.",
    "translator_finished_chapter" => "Переводчик закончил работать над этой главой, поэтому проверяющий не может быть удалён.",
    "font_format_error" => "Принимается только шрифт формата WOFF",
    "not_csv_format_error" => "Это не файл CSV!",
    "font_name_error" => "Имя файла должно содержать \"sun\" или \"backsun\" для соответствующего шрифта",
    "local_use_restriction" => "Эта функция не доступна на локальном сервере.",
    "error_zip_file_required" => "Файл должен быть в формате ZIP",
    "projects_empty_error" => "Выберите проект",
    "proj_lang_empty_error" => "Выберите язык проекта",

    // Success messages
    "you_event_finished_success" => "Вы закончили переводить все свои главы.",
    "translator_event_finished_success" => "Переводчик закончил переводить все свои главы.",
    "check_request_sent_success" => "Запрос на проверку отправлен. Страница перезагрузится, когда проверяющий примет приглашение.",
    "cotr_not_ready_to_discuss_message" => "Пожалуйста подождите вашего партнера, пока он/она не присоединится к этому шагу",
    "partner_not_ready_message" => "Ваш партнер еще не перешел к этому шагу. Когда он(а) будет готов(а) содержимое страницы обновится.",
    "successfully_applied" => "Вы были успешно подписаны на эту книгу!",
    "successfully_created" => "Успешно создано!",
    "successfully_updated" => "Успешно обновлено!",
    "successfully_deleted" => "Успешно удалено!",
    "moved_back_success" => "Переводчик успешно переведен на шаг назад!",
    "checker_removed_success" => "Проверяющий был успешно удален!",
    "import_successful_message" => "Проект был успешно импортирован!",
    "font_uploaded" => "Шрифт {0} был успешно загружен!",
    "dictionary_updated" => "Словарь был успешно обновлён!",

    // Other messages
    "alert_message" => "Сообщение",
    "no_events_message" => "В этой категории нет книг",
    "goto_event_info" => "Перейти на информационную страницу данной книги",
    "manage_event" => "Управление книгой",
    "chapter_has_translation" => "Внимание! В этой главе уже есть некоторый перевод. Если вы перейдете к \"Отрывкам\", текст будет потерян. Вы действительно хотите продолжить?",
    "next_chapter_step_note" => "Внимание! Если вы начнёте перевод следующей главы, вы всё ещё сможете продолжить проверку текущей главы, найдя её на главной странице.",

    // -------------- Translation Controller ----------------- //

    // Index method
    "verification_error" => "Ваша учетная запись не проверена модераторами. Пожалуйста ждите...",
    "verification_error_title" => "Учетная запись не потверждена",
    "chapter" => "Глава {0}",
    "download_usfm" => "Скачать в формате USFM",
    "download_markdown" => "Скачать в формате Markdown",
    "download_json" => "Скачать в формате JSON",
    "download_ts" => "Скачать в формате tStudio",
    "login_cloud_server" => "Пожалуйста, используйте логин и пароль указанного сервера. Если у вас нет аккаунта, нажмите",
    "upload_wacs" => "Отправить в WACS",
    "upload_door43" => "Отправить в Door43",
    "two_factor_auth" => "Двух-факторная авторизация",
    "cloud_otp_code" => "Код",
    "not_implemented" => "Не реализовано!",

    // Alma keyword plugin
    "to_list" => "К списку",
    "word_translations" => "Переводы слов",
    "add_word" => "Добавить слово",
    "word" => "Слово",
    "close_word" => "Закрыть слово",
    "delete_word" => "Удалить слово",
    "delete_translation" => "Удалить перевод",
    "delete_variation" => "Удалить вариант",
    "choose_word" => "выберите слово",
    "variations" => "Варианты",
    "variation" => "Вариант",
    "translation" => "Перевод",
    "votes" => "Голосов",
    "vote" => "Отдать голос",
    "unvote" => "Отозвать голос",
    "confirm_translation" => "Утвердить перевод",
    "cancel_confirmation" => "Отменить утверждение",
    "comment" => "коммент",
    "add" => "Добавить",

    // Errors
    "word_not_found_error" => "Ошибка: термин не найден.",
    "translation_approved_error" => "Ошибка: перевод уже утвержден.",
    "unknown_datatype_error" => "Ошибка: неизвестный тип данных.",
    "auth_error" => "Ошибка: авторизуйтесь, пожалуйста.",
    "already_voted_error" => "Ошибка: вы уже проголосовали.",
    "not_voted_error" => "Ошибка: вы еще не проголосовали.",
    "word_exist_error" => "Такой термин уже существует",
    "word_not_specified_error" => "Ошибка: не указано слово",

    // -------------- Bible books names ----------------- //
    "gen" => "Бытие",
    "exo" => "Исход",
    "lev" => "Левит",
    "num" => "Числа",
    "deu" => "Второзаконие",
    "jos" => "Книга Иисуса Навина",
    "jdg" => "Книга Судей Израилевых",
    "rut" => "Книга Руфи",
    "1sa" => "1-я книга Царств",
    "2sa" => "2-я книга Царств",
    "1ki" => "3-я книга Царств",
    "2ki" => "4-я книга Царств",
    "1ch" => "1-я книга Паралипоменон",
    "2ch" => "2-я книга Паралипоменон",
    "ezr" => "Книга Ездры",
    "neh" => "Книга Неемии",
    "est" => "Книга Есфири",
    "job" => "Книга Иова",
    "psa" => "Псалтирь",
    "pro" => "Притчи Соломона",
    "ecc" => "Книга Екклезиаста",
    "sng" => "Песнь песней Соломона",
    "isa" => "Книга пророка Исаии",
    "jer" => "Книга пророка Иеремии",
    "lam" => "Плач Иеремии",
    "ezk" => "Книга пророка Иезекииля",
    "dan" => "Книга пророка Даниила",
    "hos" => "Книга пророка Осии",
    "jol" => "Книга пророка Иоиля",
    "amo" => "Книга пророка Амоса",
    "oba" => "Книга пророка Авдия",
    "jon" => "Книга пророка Ионы",
    "mic" => "Книга пророка Михея",
    "nam" => "Книга пророка Наума",
    "hab" => "Книга пророка Аввакума",
    "zep" => "Книга пророка Софонии",
    "hag" => "Книга пророка Аггея",
    "zec" => "Книга пророка Захарии",
    "mal" => "Книга пророка Малахии",
    "mat" => "От Матфея",
    "mrk" => "От Марка",
    "luk" => "От Луки",
    "jhn" => "От Иоанна",
    "act" => "Деяния",
    "rom" => "Послание к Римлянам",
    "1co" => "1-е послание к Коринфянам",
    "2co" => "2-е послание к Коринфянам",
    "gal" => "Послание к Галатам",
    "eph" => "Послание к Ефесянам",
    "php" => "Послание к Филиппийцам",
    "col" => "Послание к Колоссянам",
    "1th" => "1-е послание к Фессалоникийцам",
    "2th" => "2-е послание к Фессалоникийцам",
    "1ti" => "1-е послание к Тимофею",
    "2ti" => "2-е послание к Тимофею",
    "tit" => "Послание к Титу",
    "phm" => "Послание к Филимону",
    "heb" => "Послание к Евреям",
    "jas" => "Послание Иакова",
    "1pe" => "1-е послание Петра",
    "2pe" => "2-е послание Петра",
    "1jn" => "1-е послание Иоанна",
    "2jn" => "2-е послание Иоанна",
    "3jn" => "3-е послание Иоанна",
    "jud" => "Послание Иуды",
    "rev" => "Откровение",
    "wkt" => "kt",
    "wns" => "names",
    "wot" => "other",

    // Admin Controller
    "admin_tools_title" => "Инструменты администратора",
    "update_lang_db" => "Обновление базы данных языков",
    "update_src_catalog" => "Обновление исходников",
    "go" => "Выполнить",
    "create_multiple_users" => "Создание множества пользователей",
    "tools_quantity_members" => "Количество (по умолчанию: 50)",
    "enter_value" => "Введите значение",
    "tools_member_language" => "Языки (через запятую, по умолчанию: en)",
    "enter_lang_codes" => "Введите код(ы) языков",
    "sail_dictionary_editor" => "Редактор словаря SAIL",
    "create_news" => "Создание новостей",
    "tools_news_title" => "Заголовок новости",
    "enter_news_title" => "Введите текст",
    "tools_news_category" => "Категория",
    "select_news_category" => "Выберите категорию",
    "tools_news_text" => "Текст",
    "enter_news_text" => "Введите текст",
    "create_source" => "Создать запись исходника",
    "tools_src_language" => "Язык исходника",
    "select_src_language" => "Выберите язык исходника",
    "tools_src_type" => "Тип исходника",
    "select_src_type" => "Выберите тип исходника",
    "add_custom_src" => "Не нашли?",
    "tools_src_slug" => "Код исходника",
    "enter_src_slug" => "Введите код исходника",
    "tools_src_name" => "Имя исходника",
    "enter_src_name" => "Введите имя исходника",
    "upload_source" => "Загрузить исходник",
    "update_source" => "Обновить исходник",
    "tools_src" => "Тип исходника",
    "select_src" => "Выберите тип исходника",
);
