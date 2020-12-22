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
            /*Kazakh 	                    қазақ тілі 	kk 	kaz 	kaz 	kaz
            Central Khmer 	            ខ្មែរ, ខេមរភាសា, ភាសាខ្មែរ 	km 	khm 	khm 	khm 	also known as Khmer or Cambodian
            Kikuyu, Gikuyu 	            Gĩkũyũ 	ki 	kik 	kik 	kik
            Kinyarwanda 	            Ikinyarwanda 	rw 	kin 	kin 	kin
            Kirghiz, Kyrgyz 	Кыргызча, Кыргыз тили 	ky 	kir 	kir 	kir
            Komi 	коми кыв 	kv 	kom 	kom 	kom + 2 	macrolanguage
            Kongo 	Kikongo 	kg 	kon 	kon 	kon + 3 	macrolanguage
            Korean 	한국어 	ko 	kor 	kor 	kor
            Kurdish 	Kurdî, کوردی‎ 	ku 	kur 	kur 	kur + 3 	macrolanguage
            Kuanyama, Kwanyama 	Kuanyama 	kj 	kua 	kua 	kua
            Latin 	latine, lingua latina 	la 	lat 	lat 	lat 	ancient
            Luxembourgish, Letzeburgesch 	Lëtzebuergesch 	lb 	ltz 	ltz 	ltz
            Ganda 	Luganda 	lg 	lug 	lug 	lug
            Limburgan, Limburger, Limburgish 	Limburgs 	li 	lim 	lim 	lim
            Lingala 	Lingála 	ln 	lin 	lin 	lin
            Lao 	ພາສາລາວ 	lo 	lao 	lao 	lao
            Lithuanian 	lietuvių kalba 	lt 	lit 	lit 	lit
            Luba-Katanga 	Kiluba 	lu 	lub 	lub 	lub 	also known as Luba-Shaba
            Latvian 	latviešu valoda 	lv 	lav 	lav 	lav + 2 	macrolanguage
            Manx 	Gaelg, Gailck 	gv 	glv 	glv 	glv
            Macedonian 	македонски јазик 	mk 	mkd 	mac 	mkd
            Malagasy 	fiteny malagasy 	mg 	mlg 	mlg 	mlg + 11 	macrolanguage
            Malay 	Bahasa Melayu, بهاس ملايو‎ 	ms 	msa 	may 	msa + 36 	macrolanguage, Standard Malay is [zsm], Indonesian is [id/ind]
            Malayalam 	മലയാളം 	ml 	mal 	mal 	mal
            Maltese 	Malti 	mt 	mlt 	mlt 	mlt
            Maori 	te reo Māori 	mi 	mri 	mao 	mri 	also known as Māori
            Marathi 	मराठी 	mr 	mar 	mar 	mar 	also known as Marāṭhī
            Marshallese 	Kajin M̧ajeļ 	mh 	mah 	mah 	mah
            Mongolian 	Монгол хэл 	mn 	mon 	mon 	mon + 2 	macrolanguage
            Nauru 	Dorerin Naoero 	na 	nau 	nau 	nau 	also known as Nauruan
            n 	Navajo, Navaho 	Diné bizaad 	nv 	nav 	nav 	nav
            North Ndebele 	isiNdebele 	nd 	nde 	nde 	nde 	also known as Northern Ndebele
            Nepali 	नेपाली 	ne 	nep 	nep 	nep + 2 	macrolanguage
            Ndonga 	Owambo 	ng 	ndo 	ndo 	ndo
            Norwegian Bokmål 	Norsk Bokmål 	nb 	nob 	nob 	nob 	Covered by macrolanguage [no/nor]
            Norwegian Nynorsk 	Norsk Nynorsk 	nn 	nno 	nno 	nno 	Covered by macrolanguage [no/nor]
            Norwegian 	Norsk 	no 	nor 	nor 	nor + 2 	macrolanguage, Bokmål is [nb/nob], Nynorsk is [nn/nno]
            Sichuan Yi, Nuosu 	ꆈꌠ꒿ Nuosuhxop 	ii 	iii 	iii 	iii 	Standard form of Yi languages
            South Ndebele 	isiNdebele 	nr 	nbl 	nbl 	nbl 	also known as Southern Ndebele
            Occitan 	occitan, lenga d'òc 	oc 	oci 	oci 	oci
            Ojibwa 	ᐊᓂᔑᓈᐯᒧᐎᓐ 	oj 	oji 	oji 	oji + 7 	macrolanguage, also known as Ojibwe
            Church Slavic, Old Slavonic, Church Slavonic, Old Bulgarian, Old Church Slavonic 	ѩзыкъ словѣньскъ 	cu 	chu 	chu 	chu 	ancient, in use by Orthodox Church
            Oromo 	Afaan Oromoo 	om 	orm 	orm 	orm + 4 	macrolanguage
            Oriya 	ଓଡ଼ିଆ 	or 	ori 	ori 	ori + 2 	macrolanguage, also known as Odia
            Ossetian, Ossetic 	ирон æвзаг 	os 	oss 	oss 	oss
            Punjabi, Panjabi 	ਪੰਜਾਬੀ, پنجابی‎ 	pa 	pan 	pan 	pan
            Pali 	पालि, पाळि 	pi 	pli 	pli 	pli 	ancient, also known as Pāli
            Persian فارسی fa 	fas 	per 	fas + 2 	macrolanguage, also known as Farsi
            Polish 	język polski, polszczyzna 	pl 	pol 	pol 	pol
            Pashto, Pushto پښتو ps 	pus 	pus 	pus + 3 	macrolanguage
            Portuguese 	Português 	pt 	por 	por 	por
            Quechua 	Runa Simi, Kichwa 	qu 	que 	que 	que + 43 	macrolanguage
            Romansh 	Rumantsch Grischun 	rm 	roh 	roh 	roh
            Rundi 	Ikirundi 	rn 	run 	run 	run 	also known as Kirundi
            Romanian, Moldavian, Moldovan 	Română 	ro 	ron 	rum 	ron 	The identifiers mo and mol are deprecated, leaving ro and ron (639-2/T) and rum (639-2/B) the current language identifiers to be used for the variant of the Romanian language also known as Moldavian and Moldovan in English and moldave in French. The identifiers mo and mol will not be assigned to different items, and recordings using these identifiers will not be invalid.
            Russian 	русский 	ru 	rus 	rus 	rus
            Sanskrit 	संस्कृतम् 	sa 	san 	san 	san 	ancient, still spoken, also known as Saṃskṛta
            Sardinian 	sardu 	sc 	srd 	srd 	srd + 4 	macrolanguage
            Sindhi 	सिन्धी, سنڌي، سندھی‎ 	sd 	snd 	snd 	snd
            Northern Sami 	Davvisámegiella 	se 	sme 	sme 	sme
            Samoan 	gagana fa'a Samoa 	sm 	smo 	smo 	smo
            yângâ tî sängö 	sg 	sag 	sag 	sag
            Serbian 	српски језик 	sr 	srp 	srp 	srp 	The ISO 639-2/T code srp deprecated the ISO 639-2/B code scc[2]
            Gaelic, Scottish Gaelic 	Gàidhlig 	gd 	gla 	gla 	gla
            Shona 	chiShona 	sn 	sna 	sna 	sna
            Sinhala, Sinhalese 	සිංහල 	si 	sin 	sin 	sin
            Slovak 	Slovenčina, Slovenský jazyk 	sk 	slk 	slo 	slk
            Slovenian 	Slovenski jezik, Slovenščina 	sl 	slv 	slv 	slv 	also known as Slovene
            Somali 	Soomaaliga, af Soomaali 	so 	som 	som 	som
            Southern Sotho 	Sesotho 	st 	sot 	sot 	sot
            Spanish, Castilian 	Español 	es 	spa 	spa 	spa
            Sundanese 	Basa Sunda 	su 	sun 	sun 	sun
            Swahili 	Kiswahili 	sw 	swa 	swa 	swa + 2 	macrolanguage
            Swati 	SiSwati 	ss 	ssw 	ssw 	ssw 	also known as Swazi
            Swedish 	Svenska 	sv 	swe 	swe 	swe
            Tamil 	தமிழ் 	ta 	tam 	tam 	tam
            Telugu 	తెలుగు 	te 	tel 	tel 	tel
            Tajik 	тоҷикӣ, toçikī, تاجیکی‎ 	tg 	tgk 	tgk 	tgk
            Thai 	ไทย 	th 	tha 	tha 	tha
            Tigrinya 	ትግርኛ 	ti 	tir 	tir 	tir
            Tibetan 	བོད་ཡིག 	bo 	bod 	tib 	bod 	also known as Standard Tibetan
            Turkmen 	Türkmen, Түркмен 	tk 	tuk 	tuk 	tuk
            Tagalog 	Wikang Tagalog 	tl 	tgl 	tgl 	tgl 	Note: Filipino (Pilipino) has the code [fil]
            Tswana 	Setswana 	tn 	tsn 	tsn 	tsn
            Tonga (Tonga Islands) 	Faka Tonga 	to 	ton 	ton 	ton 	also known as Tongan
            Turkish 	Türkçe 	tr 	tur 	tur 	tur
            Tsonga 	Xitsonga 	ts 	tso 	tso 	tso
            Tatar 	татар теле, tatar tele 	tt 	tat 	tat 	tat
            Twi 	Twi 	tw 	twi 	twi 	twi 	Covered by macrolanguage [ak/aka]
            Tahitian 	Reo Tahiti 	ty 	tah 	tah 	tah 	One of the Reo Mā`ohi (languages of French Polynesia)
            Uighur, Uyghur 	ئۇيغۇرچە‎, Uyghurche 	ug 	uig 	uig 	uig
            Ukrainian 	Українська 	uk 	ukr 	ukr 	ukr
            Urdu اردو ur 	urd 	urd 	urd
            Oʻzbek, Ўзбек, أۇزبېك‎ 	uz 	uzb 	uzb 	uzb + 2 	macrolanguage
            Venda 	Tshivenḓa 	ve 	ven 	ven 	ven
            Vietnamese 	Tiếng Việt 	vi 	vie 	vie 	vie
            Volapük 	Volapük 	vo 	vol 	vol 	vol 	constructed
            Walloon 	Walon 	wa 	wln 	wln 	wln
            Welsh 	Cymraeg 	cy 	cym 	wel 	cym
            Wolof 	Wollof 	wo 	wol 	wol 	wol
            Western Frisian 	Frysk 	fy 	fry 	fry 	fry 	also known as Frisian
            Xhosa 	isiXhosa 	xh 	xho 	xho 	xho
            Yiddish ייִדיש yi 	yid 	yid 	yid + 2 	macrolanguage. Changed in 1989 from original ISO 639:1988, ji.[1]
            Yoruba 	Yorùbá 	yo 	yor 	yor 	yor
            Zhuang, Chuang 	Saɯ cueŋƅ, Saw cuengh 	za 	zha 	zha 	zha + 16 	macrolanguage*/
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
