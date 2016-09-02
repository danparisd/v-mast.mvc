<?php
/**
 * Sample language
 */
return array(

	// Index method
	'members_title' => 'Пользователи',
	'firstName' => 'Имя',
    'lastName' => 'Фамилия',
	'password' => 'Пароль',
	'confirm_password' => 'Подтверждение пароля',
    "enter_new_password" => "Введите новый пароль",
    'userName' => 'Имя пользователя',
    'accept_btn' => 'Принять',
    'deny_btn' => 'Отмена',
    'tou' => 'Условия использования',
    'sof' => 'Утверждение веры',
    'welcome_title' => 'Добро пожаловать',
    'translator' => 'Переводчик',
    'checker' => 'Проверяющий',
    'facilitator' => 'Фасилитатор',
    'l2_checker' => 'Проверяющий 2-го уровня',
    'l3_checker' => 'Проверяющий 3-го уровня',
    'captcha_wrong' => 'Капча решена не верно',
    'userType_wrong_error' => 'Тип пользователя не верен',
    'success' => 'Успешно',

	// Activate method
	'activate_account_title' => 'Активация учетной записи',
	"resend_activation_code" => "Не получили сообщение? Отправить еще раз.",
	"wrong_activation_email" => "Неверный электронный адрес для активации. Учетная запись с этим электронным адресом не существует либо уже активирована",

	// Login method
	'wrong_credentials_error' => 'Неверный адрес/имя пользователя или пароль, или учетная запись не активирована',
	'login' => 'Войти',
	'signup' => 'Регистрация',
	'logout' => 'Выйти',
	'login_message' => 'Регистрация',
	'already_member' => 'Уже являетесь пользователем?',
	"dont_have_account" => "Нет учетной записи?",
    'login_title' => 'Авторизация',
    'forgot_password' => 'Забыли пароль?',
	"profile_message" => "Профиль",
	"common_skills" => "Общие",
	"facilitator_skills" => "Фасилитатор",
	"checker_skills" => "Последующие уровни проверки",
	"save" => "Сохранить",
	"weak" => "слабое",
	"moderate" => "среднее",
	"strong" => "хорошее",
	"expert" => "профессиональное",
	"fluent" => "отличное",
	"native" => "родной язык",
	"limited" => "начальное",
	"none" => "никакое",
	"less_than" => "менее {0}",
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
	"select_language" => "Выберите язык",
	"language_fluency" => "Оцените свой уровень владения языком",
	"lang_geographic_years" => "Сколько времени из последних 10 лет вашей жизни вы прожили на территории, где распространен данный язык?",
	"bbl_trans_yrs" => "Сколько лет вы занимаетесь переводом Библии?",
	"othr_trans_yrs" => "Сколько лет вы занимаетесь другими видами перевода?",
	"bbl_knwlg_degr" => "Оцените свой уровень знания Библии",
	"mast_evnts" => "В каком количестве мероприятий MAST вы принимали участие",
	"mast_role" => "Какую роль вы прежде исполняли в ходе мероприятий MAST",
	"teamwork" => "Как часто вы работаете в команде?",
	"org" => "Представителем какой организации вы являетесь?",
	"ref_person" => "Фамилия/Имя представителя",
	"ref_email" => "Электронная почта представителя",
	"mast_facilitator" => "Проходили ли вы обучение относительно групповой работы в MAST?",
	"education" => "Если у вас есть духовное образование, отметьте нужное",
	"ed_area" => "Выберите область, которая относится к вам",
	"ed_place" => "Образовательное учреждение",
	"orig_langs" => "Оцените свой уровень владения исконными языками Библии",
	"hebrew_knwlg" => "Древнееврейский",
	"greek_knwlg" => "Древнегреческий",
	"church_role" => "Какое служение вы исполняете в Церкви?",

    // Passwordreset method
    'passwordreset_title' => 'Сброс пароля',
	'continue' => 'Продолжить',
	'enter_email' => 'Введите Email',

	// Success messages
	"update_profile_success" => "Profile has been updated successfully",
    'passwordreset_link_message' => 'Чтобы сбросить пароль, перейдите по ссылке. <a href="{0}">{1}</a>',
    'pwresettoken_send_success' => 'Вам было отправлено письмо с инструкциями по сбросу пароля. Если его нет, проверьте в папке спам.',
    'password_reset_success' => 'Ваш пароль был успешно изменен. Теперь вы можете войти, используя новый пароль <a href="{0}">Войти</a>',
    'activation_link_message' => '<h3>Спасибо за регистрацию!</h3>'."\n".' Чтобы активировать учетную запись перейдите по этой ссылке. <a href="{0}">{1}</a>',
    'account_activated_success' => 'Учетная запись активирована. Теперь вы можете <a href="{0}">Войти</a>',
    "resend_activation_success_message" => "Сообщение с кодом активации отправлено на вашу электронную почту. Если письма нет во входящих, проверьте в папке спам.",
    'registration_success_message' => 'Регистрация прошла успешно! Проверьте почту для активации учетной записи. Если письма нет во входящих, проверьте в папке спам.',

    // Error messages
    "required_fields_empty_error" => "Please fill in all the required fields",
    "update_profile_error" => "Profile hasn't been updated",
    'token_expired_error' => 'Код сброса пароля просрочен',
    'update_table_error' => 'Ошибка записи в базу данных. Пожалуйста, попробуйте снова.',
    'no_account_error' => 'Учетная запись не существует или код недействителен',
    'account_activated_error' => 'Account has already been activated',
    'invalid_link_error' => 'Неверная ссылка активации',
    'userName_characters_error' => 'Имя пользователя должно содержать только буквы латиницы и числа, а также начинаться с букв',
    'userName_length_error' => 'Имя пользователя должно быть длиной от 5 до 20 символов',
    'firstName_length_error' => 'Имя должно быть длиной от 2 до 20 символов',
    'lastName_length_error' => 'Фамилия должна быть длиной от 2 до 20 символов',
    'enter_valid_email_error' => 'Введите правильный почтовый адрес',
    'email_taken_error' => 'Почтовый адрес уже используется',
    "username_taken_error" => "Имя пользователя уже используется",
    'password_short_error' => 'Пароль слишком короткий',
    'passwords_notmatch_error' => 'Пароли не совпадают',
    'tou_accept_error' => 'Вы должны принять Условия Использования',
    'sof_accept_error' => 'Вы должны принять Утверждение Веры',
);
