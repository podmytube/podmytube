<?php

use App\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('languages')->delete();
        /**
         * Parents categories
         */
        $data = [
            ['iso_name' => 'Abkhazian', 'native_name' => 'аҧсуа бызшәа, аҧсшәа', 'code' => 'ab'],
            ['iso_name' => 'Afar', 'native_name' => 'Afaraf', 'code' => 'aa'],
            ['iso_name' => 'Afrikaans', 'native_name' => 'Afrikaans', 'code' => 'af'],
            ['iso_name' => 'Akan', 'native_name' => 'Akan', 'code' => 'ak'],
            ['iso_name' => 'Albanian', 'native_name' => 'Shqip', 'code' => 'sq'],
            ['iso_name' => 'Amharic', 'native_name' => 'አማርኛ', 'code' => 'am'],
            ['iso_name' => 'Arabic', 'native_name' => 'العربية', 'code' => 'ar'],
            ['iso_name' => 'Aragonese', 'native_name' => 'aragonés', 'code' => 'an'],
            ['iso_name' => 'Armenian', 'native_name' => 'Հայերեն', 'code' => 'hy'],
            ['iso_name' => 'Assamese', 'native_name' => 'অসমীয়া', 'code' => 'as'],
            ['iso_name' => 'Avaric', 'native_name' => 'авар мацӀ, магӀарул мацӀ', 'code' => 'av'],
            ['iso_name' => 'Avestan', 'native_name' => 'avesta', 'code' => 'ae'],
            ['iso_name' => 'Aymara', 'native_name' => 'aymar aru', 'code' => 'ay'],
            ['iso_name' => 'Azerbaijani', 'native_name' => 'azərbaycan dili', 'code' => 'az'],
            ['iso_name' => 'Bambara', 'native_name' => 'bamanankan', 'code' => 'bm'],
            ['iso_name' => 'Bashkir', 'native_name' => 'башҡорт теле', 'code' => 'ba'],
            ['iso_name' => 'isolate', 'native_name' => 'Basque euskara, euskera', 'code' => 'eu'],
            ['iso_name' => 'Belarusian', 'native_name' => 'беларуская мова', 'code' => 'be'],
            ['iso_name' => 'Bengali', 'native_name' => 'বাংলা', 'code' => 'bn'],
            ['iso_name' => 'Bihari languages', 'native_name' => 'भोजपुरी', 'code' => 'bh'],
            ['iso_name' => 'Bislama', 'native_name' => 'Bislama', 'code' => 'bi'],
            ['iso_name' => 'Bosnian', 'native_name' => 'bosanski jezik', 'code' => 'bs'],
            ['iso_name' => 'Breton', 'native_name' => 'brezhoneg', 'code' => 'br'],
            ['iso_name' => 'Bulgarian', 'native_name' => 'български език', 'code' => 'bg'],
            ['iso_name' => 'Burmese', 'native_name' => 'ဗမာစာ', 'code' => 'my'],
            ['iso_name' => 'Catalan, Valencian', 'native_name' => 'català, valencià', 'code' => 'ca'],
            ['iso_name' => 'Chamorro', 'native_name' => 'Chamoru', 'code' => 'ch'],
            ['iso_name' => 'Chechen', 'native_name' => 'нохчийн мотт', 'code' => 'ce'],
            ['iso_name' => 'Chichewa, Chewa, Nyanja', 'native_name' => 'chiCheŵa, chinyanja', 'code' => 'ny'],
            ['iso_name' => 'Chinese', 'native_name' => '中文 (Zhōngwén), 汉语, 漢語', 'code' => 'zh'],
            ['iso_name' => 'Chuvash', 'native_name' => 'чӑваш чӗлхи', 'code' => 'cv'],
            ['iso_name' => 'Cornish', 'native_name' => 'Kernewek', 'code' => 'kw'],
            ['iso_name' => 'Corsican', 'native_name' => 'corsu, lingua corsa', 'code' => 'co'],
            ['iso_name' => 'Cree', 'native_name' => 'ᓀᐦᐃᔭᐍᐏᐣ', 'code' => 'cr'],
            ['iso_name' => 'Croatian', 'native_name' => 'hrvatski jezik', 'code' => 'hr'],
            ['iso_name' => 'Czech', 'native_name' => 'čeština, český jazyk', 'code' => 'cs'],
            ['iso_name' => 'Danish', 'native_name' => 'dansk', 'code' => 'da'],
            ['iso_name' => 'Divehi, Dhivehi, Maldivian', 'native_name' => 'ދިވެހި', 'code' => 'dv'],
            ['iso_name' => 'Dutch, Flemish', 'native_name' => 'Nederlands, Vlaams', 'code' => 'nl'],
            ['iso_name' => 'Dzongkha', 'native_name' => 'རྫོང་ཁ', 'code' => 'dz'],
            ['iso_name' => 'English', 'native_name' => 'English', 'code' => 'en'],
            ['iso_name' => 'Esperanto', 'native_name' => 'Esperanto', 'code' => 'eo'],
            ['iso_name' => 'Estonian', 'native_name' => 'eesti, eesti keel', 'code' => 'et'],
            ['iso_name' => 'Ewe', 'native_name' => 'Eʋegbe', 'code' => 'ee'],
            ['iso_name' => 'Faroese', 'native_name' => 'føroyskt', 'code' => 'fo'],
            ['iso_name' => 'Fijian', 'native_name' => 'vosa Vakaviti', 'code' => 'fj'],
            ['iso_name' => 'Finnish', 'native_name' => 'suomi, suomen kieli', 'code' => 'fi'],
            ['iso_name' => 'French', 'native_name' => 'français, langue française', 'code' => 'fr'],
            ['iso_name' => 'Fulah', 'native_name' => 'Fulfulde, Pulaar, Pular', 'code' => 'ff'],
            ['iso_name' => 'Galician', 'native_name' => 'Galego', 'code' => 'gl'],
            ['iso_name' => 'Georgian', 'native_name' => 'ქართული', 'code' => 'ka'],
            ['iso_name' => 'German', 'native_name' => 'Deutsch', 'code' => 'de'],
            ['iso_name' => 'Greek, Modern (1453–)', 'native_name' => 'ελληνικά', 'code' => 'el'],
            ['iso_name' => 'Guarani', 'native_name' => "Avañe'ẽ", 'code' => 'gn'],
            ['iso_name' => 'Gujarati', 'native_name' => 'ગુજરાતી', 'code' => 'gu'],
            ['iso_name' => 'Haitian, Haitian Creole', 'native_name' => 'Kreyòl ayisyen', 'code' => 'ht'],
            ['iso_name' => 'Hausa (Hausa)', 'native_name' => 'هَوُسَ', 'code' => 'ha'],
            ['iso_name' => 'Hebrew', 'native_name' => 'עברית', 'code' => 'he'],
            ['iso_name' => 'Herero', 'native_name' => 'Otjiherero', 'code' => 'hz'],
            ['iso_name' => 'Hindi', 'native_name' => 'हिन्दी, हिंदी', 'code' => 'hi'],
            ['iso_name' => 'Hiri Motu', 'native_name' => 'Hiri Motu', 'code' => 'ho'],
            ['iso_name' => 'Hungarian', 'native_name' => 'magyar', 'code' => 'hu'],
            ['iso_name' => 'Interlingua (International Auxiliary Language Association)', 'native_name' => 'Interlingua', 'code' => 'ia'],
            ['iso_name' => 'Indonesian', 'native_name' => 'Bahasa Indonesia', 'code' => 'id'],
            ['iso_name' => 'Interlingue, Occidental', 'native_name' => '(originally:) Occidental, (after WWII:) Interlingue', 'code' => 'ie'],
            ['iso_name' => 'Irish', 'native_name' => 'Gaeilge', 'code' => 'ga'],
            ['iso_name' => 'Igbo', 'native_name' => 'Asụsụ Igbo', 'code' => 'ig'],
            ['iso_name' => 'Inupiaq', 'native_name' => 'Iñupiaq, Iñupiatun', 'code' => 'ik'],
            ['iso_name' => 'Ido', 'native_name' => 'Ido', 'code' => 'io'],
            ['iso_name' => 'Icelandic', 'native_name' => 'Íslenska', 'code' => 'is'],
            ['iso_name' => 'Italian', 'native_name' => 'Italiano', 'code' => 'it'],
            ['iso_name' => 'Inuktitut', 'native_name' => 'ᐃᓄᒃᑎᑐᑦ', 'code' => 'iu'],
            ['iso_name' => 'Japanese', 'native_name' => '日本語 (にほんご)', 'code' => 'ja'],
            ['iso_name' => 'Javanese', 'native_name' => 'ꦧꦱꦗꦮ, Basa Jawa', 'code' => '  jv'],
            ['iso_name' => 'Kalaallisut, Greenlandic', 'native_name' => 'kalaallisut, kalaallit oqaasii', 'code' => 'kl'],
            ['iso_name' => 'Kannada', 'native_name' => 'ಕನ್ನಡ', 'code' => 'kn'],
            ['iso_name' => 'Kanuri', 'native_name' => 'Kanuri', 'code' => 'kr'],
            ['iso_name' => 'Kashmiri', 'native_name' => 'कश्मीरी, كشميري‎', 'code' => 'ks'],
            ['iso_name' => 'Kazakh', 'native_name' => 'қазақ тілі', 'code' => 'kk'],
            ['iso_name' => 'Central Khmer', 'native_name' => 'ខ្មែរ, ខេមរភាសា, ភាសាខ្មែរ', 'code' => 'km'],
            ['iso_name' => 'Kikuyu, Gikuyu', 'native_name' => 'Gĩkũyũ', 'code' => 'ki'],
            ['iso_name' => 'Kinyarwanda', 'native_name' => 'Ikinyarwanda', 'code' => 'rw'],
            ['iso_name' => 'Kirghiz, Kyrgyz', 'native_name' => 'Кыргызча, Кыргыз тили',	'code' => 'ky'],
            ['iso_name' => 'Komi', 'native_name' => 'коми кыв', 'code' => 'kv'],
            ['iso_name' => 'Kongo', 'native_name' => 'Kikongo', 'code' => 'kg'],
            ['iso_name' => 'Korean', 'native_name' => '한국어', 'code' => 'ko'],
            ['iso_name' => 'Kurdish', 'native_name' => 'Kurdî, کوردی‎', 'code' => 'ku'],
            ['iso_name' => 'Kuanyama, Kwanyama', 'native_name' => 'Kuanyama', 'code' => 'kj'],
            ['iso_name' => 'Latin',  'native_name' => 'latine, lingua latina', 'code' => 'la'],
            ['iso_name' => 'Luxembourgish, Letzeburgesch',	'native_name' => 'Lëtzebuergesch', 'code' => 'lb'],
            ['iso_name' => 'Ganda',	'native_name' => 'Luganda', 'code' => 'lg'],
            ['iso_name' => 'Limburgan, Limburger, Limburgish',	'native_name' => 'Limburgs', 'code' => 'li'],
            ['iso_name' => 'Lingala',	'native_name' => 'Lingála', 'code' => 'ln'],
            ['iso_name' => 'Lao',	'native_name' => 'ພາສາລາວ', 'code' => 'lo'],
            ['iso_name' => 'Lithuanian',	'native_name' => 'lietuvių kalba', 'code' => 'lt'],
            ['iso_name' => 'Luba-Katanga',	'native_name' => 'Kiluba', 'code' => 'lu'],
            ['iso_name' => 'Latvian',	'native_name' => 'latviešu valoda', 'code' => 'lv'],
            ['iso_name' => 'Manx',	'native_name' => 'Gaelg, Gailck', 'code' => 'gv'],
            ['iso_name' => 'Macedonian', 'native_name' => 'македонски јазик', 'code' => 'mk'],
            ['iso_name' => 'Malagasy', 	'native_name' => 'fiteny malagasy', 'code' => 'mg'],
            ['iso_name' => 'Malay', 	'native_name' => 'Bahasa Melayu, بهاس ملايو‎', 'code' => 'ms'],
            ['iso_name' => 'Malayalam', 	'native_name' => 'മലയാളം', 'code' => 'ml'],
            ['iso_name' => 'Maltese', 	'native_name' => 'Malti', 'code' => 'mt'],
            ['iso_name' => 'Maori', 	'native_name' => 'te reo Māori', 'code' => 'mi'],
            ['iso_name' => 'Marathi', 	'native_name' => 'मराठी', 'code' => 'mr'],
            ['iso_name' => 'Marshallese', 	'native_name' => 'Kajin M̧ajeļ', 'code' => 'mh'],
            ['iso_name' => 'Mongolian', 	'native_name' => 'Монгол хэл', 'code' => 'mn'],
            ['iso_name' => 'Nauru', 	'native_name' => 'Dorerin Naoero', 'code' => 'na'],
            ['iso_name' => 'Navajo, Navaho' , 'native_name'=> 'Diné bizaad', 'code' =>'nv'],
            ['iso_name' => 'North Ndebele' ,'native_name'=>	'isiNdebele','code'=> 'nd'],
            ['iso_name' => 'Nepali' ,'native_name'=>	'नेपाली 	ne 	nep 	nep 	nep + 2 	macrolanguage
            ['iso_name' => 'Ndonga' ,'native_name'=>	'Owambo 	ng 	ndo 	ndo 	ndo
            ['iso_name' => 'Norwegian Bokmål' ,'native_name'=>	'Norsk Bokmål 	nb 	nob 	nob 	nob 	Covered by macrolanguage [no/nor]
            ['iso_name' => 'Norwegian Nynorsk' ,'native_name'=>	'Norsk Nynorsk 	nn 	nno 	nno 	nno 	Covered by macrolanguage [no/nor]
            ['iso_name' => 'Norwegian' ,'native_name'=>	'Norsk 	no 	nor 	nor 	nor + 2 	macrolanguage, Bokmål is [nb/nob], Nynorsk is [nn/nno]
            ['iso_name' => 'Sichuan Yi, Nuosu' ,'native_name'=>	'ꆈꌠ꒿ Nuosuhxop 	ii 	iii 	iii 	iii 	Standard form of Yi languages
            ['iso_name' => 'South Ndebele' ,'native_name'=>	'isiNdebele 	nr 	nbl 	nbl 	nbl 	also known as Southern Ndebele
            ['iso_name' => 'Occitan' ,'native_name'=>	'occitan, lenga d'òc 	oc 	oci 	oci 	oci
            ['iso_name' => 'Ojibwa' ,'native_name'=>	'ᐊᓂᔑᓈᐯᒧᐎᓐ 	oj 	oji 	oji 	oji + 7 	macrolanguage, also known as Ojibwe
            ['iso_name' => 'Church Slavic, Old Slavonic, Church Slavonic, Old Bulgarian, Old Church Slavonic' ,'native_name'=>	'ѩзыкъ словѣньскъ 	cu 	chu 	chu 	chu 	ancient, in use by Orthodox Church
            ['iso_name' => 'Oromo' ,'native_name'=>	'Afaan Oromoo 	om 	orm 	orm 	orm + 4 	macrolanguage
            ['iso_name' => 'Oriya' ,'native_name'=>	'ଓଡ଼ିଆ 	or 	ori 	ori 	ori + 2 	macrolanguage, also known as Odia
            ['iso_name' => 'Ossetian, Ossetic' ,'native_name'=>	'ирон æвзаг 	os 	oss 	oss 	oss
            ['iso_name' => 'Punjabi, Panjabi' ,'native_name'=>	'ਪੰਜਾਬੀ, پنجابی‎ 	pa 	pan 	pan 	pan
            ['iso_name' => 'Pali' ,'native_name'=>	'पालि, पाळि 	pi 	pli 	pli 	pli 	ancient, also known as Pāli
            ['iso_name' => 'Persian' ,'native_name'=>	'فارسی fa 	fas 	per 	fas + 2 	macrolanguage, also known as Farsi
            ['iso_name' => 'Polish' ,'native_name'=>	'język polski, polszczyzna 	pl 	pol 	pol 	pol
            ['iso_name' => 'Pashto, Pushto' ,'native_name'=>	'پښتو ps 	pus 	pus 	pus + 3 	macrolanguage
            ['iso_name' => 'Portuguese' ,'native_name'=>	'Português 	pt 	por 	por 	por
            ['iso_name' => 'Quechua' ,'native_name'=>	'Runa Simi, Kichwa 	qu 	que 	que 	que + 43 	macrolanguage
            ['iso_name' => 'Romansh' ,'native_name'=>	'Rumantsch Grischun 	rm 	roh 	roh 	roh
            ['iso_name' => 'Rundi' ,'native_name'=>	'Ikirundi 	rn 	run 	run 	run 	also known as Kirundi
            ['iso_name' => 'Romanian, Moldavian, Moldovan' ,'native_name'=>	'Română 	ro 	ron 	rum 	ron 	The identifiers mo and mol are deprecated, leaving ro and ron (639-2/T) and rum (639-2/B) the current language identifiers to be used for the variant of the Romanian language also known as Moldavian and Moldovan in English and moldave in French. The identifiers mo and mol will not be assigned to different items, and recordings using these identifiers will not be invalid.
            ['iso_name' => 'Russian' ,'native_name'=>	'русский 	ru 	rus 	rus 	rus
            ['iso_name' => 'Sanskrit' ,'native_name'=>	'संस्कृतम् 	sa 	san 	san 	san 	ancient, still spoken, also known as Saṃskṛta
            ['iso_name' => 'Sardinian' ,'native_name'=>	'sardu 	sc 	srd 	srd 	srd + 4 	macrolanguage
            ['iso_name' => 'Sindhi' ,'native_name'=>	'सिन्धी, سنڌي، سندھی‎ 	sd 	snd 	snd 	snd
            ['iso_name' => 'Northern Sami' ,'native_name'=>	'Davvisámegiella 	se 	sme 	sme 	sme
            ['iso_name' => 'Samoan' ,'native_name'=>	"gagana fa'a Samoa" 	sm 	smo 	smo 	smo
            ['iso_name' => 'Sango ,'native_name'=>	'yângâ tî sängö 	sg 	sag 	sag 	sag
            ['iso_name' => 'Serbian' ,'native_name'=>	'српски језик 	sr 	srp 	srp 	srp 	The ISO 639-2/T code srp deprecated the ISO 639-2/B code scc[2]
            ['iso_name' => 'Gaelic, Scottish Gaelic' ,'native_name'=>	'Gàidhlig 	gd 	gla 	gla 	gla
            ['iso_name' => 'Shona' ,'native_name'=>	'chiShona 	sn 	sna 	sna 	sna
            ['iso_name' => 'Sinhala, Sinhalese' ,'native_name'=>	'සිංහල 	si 	sin 	sin 	sin
            ['iso_name' => 'Slovak' ,'native_name'=>	'Slovenčina, Slovenský' ,'native_name'=>	'jazyk 	sk 	slk 	slo 	slk
            ['iso_name' => 'Slovenian' ,'native_name'=>	'Slovenski jezik, Slovenščina 	sl 	slv 	slv 	slv 	also known as Slovene
            ['iso_name' => 'Somali' ,'native_name'=>	'Soomaaliga, af Soomaali 	so 	som 	som 	som
            ['iso_name' => 'Southern Sotho' ,'native_name'=>	'Sesotho 	st 	sot 	sot 	sot
            ['iso_name' => 'Spanish, Castilian' ,'native_name'=>	'Español 	es 	spa 	spa 	spa
            ['iso_name' => 'Sundanese' ,'native_name'=>	'Basa Sunda 	su 	sun 	sun 	sun
            ['iso_name' => 'Swahili' ,'native_name'=>	'Kiswahili 	sw 	swa 	swa 	swa + 2 	macrolanguage
            ['iso_name' => 'Swati' ,'native_name'=>	'SiSwati 	ss 	ssw 	ssw 	ssw 	also known as Swazi
            ['iso_name' => 'Swedish' ,'native_name'=>	'Svenska 	sv 	swe 	swe 	swe
            ['iso_name' => 'Tamil' ,'native_name'=>	'தமிழ் 	ta 	tam 	tam 	tam
            ['iso_name' => 'Telugu' ,'native_name'=>	'తెలుగు 	te 	tel 	tel 	tel
            ['iso_name' => 'Tajik' ,'native_name'=>	'тоҷикӣ, toçikī, تاجیکی‎ 	tg 	tgk 	tgk 	tgk
            ['iso_name' => 'Thai' ,'native_name'=>	'ไทย 	th 	tha 	tha 	tha
            ['iso_name' => 'Tigrinya' ,'native_name'=>	'ትግርኛ 	ti 	tir 	tir 	tir
            ['iso_name' => 'Tibetan' ,'native_name'=>	'བོད་ཡིག 	bo 	bod 	tib 	bod 	also known as Standard Tibetan
            ['iso_name' => 'Turkmen' ,'native_name'=>	'Türkmen, Түркмен 	tk 	tuk 	tuk 	tuk
            ['iso_name' => 'Tagalog' ,'native_name'=>	'Wikang Tagalog 	tl 	tgl 	tgl 	tgl 	Note: Filipino (Pilipino) has the code [fil]
            ['iso_name' => 'Tswana' ,'native_name'=>	'Setswana 	tn 	tsn 	tsn 	tsn
            ['iso_name' => 'Tonga (Tonga Islands)' ,'native_name'=>	'Faka Tonga 	to 	ton 	ton 	ton 	also known as Tongan
            ['iso_name' => 'Turkish 	Türkçe 	tr 	tur 	tur 	tur
            ['iso_name' => 'Tsonga 	Xitsonga 	ts 	tso 	tso 	tso
            ['iso_name' => 'Tatar 	татар теле, tatar tele 	tt 	tat 	tat 	tat
            ['iso_name' => 'Twi 	Twi 	tw 	twi 	twi 	twi 	Covered by macrolanguage [ak/aka]
            ['iso_name' => 'Tahitian 	Reo Tahiti 	ty 	tah 	tah 	tah 	One of the Reo Mā`ohi (languages of French Polynesia)
            ['iso_name' => 'Uighur, Uyghur 	ئۇيغۇرچە‎, Uyghurche 	ug 	uig 	uig 	uig
            ['iso_name' => 'Ukrainian 	Українська 	uk 	ukr 	ukr 	ukr
            ['iso_name' => 'Urdu اردو ur 	urd 	urd 	urd
            ['iso_name' => 'Oʻzbek, Ўзбек, أۇزبېك‎ 	uz 	uzb 	uzb 	uzb + 2 	macrolanguage
            ['iso_name' => 'Venda 	Tshivenḓa 	ve 	ven 	ven 	ven
            ['iso_name' => 'Vietnamese 	Tiếng Việt 	vi 	vie 	vie 	vie
            ['iso_name' => 'Volapük 	Volapük 	vo 	vol 	vol 	vol 	constructed
            ['iso_name' => 'Walloon 	Walon 	wa 	wln 	wln 	wln
            ['iso_name' => 'Welsh 	Cymraeg 	cy 	cym 	wel 	cym
            ['iso_name' => 'Wolof 	Wollof 	wo 	wol 	wol 	wol
            ['iso_name' => 'Western Frisian 	Frysk 	fy 	fry 	fry 	fry 	also known as Frisian
            ['iso_name' => 'Xhosa 	isiXhosa 	xh 	xho 	xho 	xho
            ['iso_name' => 'Yiddish ייִדיש yi 	yid 	yid 	yid + 2 	macrolanguage. Changed in 1989 from original ISO 639:1988, ji.[1]
            ['iso_name' => 'Yoruba 	Yorùbá 	yo 	yor 	yor 	yor
            ['iso_name' => 'Zhuang, Chuang 	Saɯ cueŋƅ, Saw cuengh 	za 	zha 	zha 	zha + 16 	macrolanguage*/
            ['iso_name' => 'Zulu', 'native_name' => 'isiZulu', 'code' => 'zu'],
        ];

        $index = 1;
        $data = array_map(
            function ($item) use (&$index) {
                return array_merge($item, [
                    'id' => $index++,
                ]);
            },
            $data
        );
        Language::insert($data);
    }
}
