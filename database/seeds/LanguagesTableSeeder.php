<?php

use App\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            Abkhazian 	                        ab
            Afar 	                            aa
            Afrikaans 	                        af
            Akan 	                            ak
            Albanian 	                        sq
            Amharic 	                        am
            Arabic                              ar
            Aragonese 	                        an
            Armenian 	                        hy
            Assamese 	                        as
            Avaric 	                            av
            Avestan 	                        ae
            Aymara 	                            ay
            Azerbaijani                         az
            Bambara 	                        bm
            Bashkir 	                        ba
            isolate 	                        eu
            Belarusian 	                        be
            Bengali 	                        bn
            Bihari languages 	                bh
            Bislama 	                        bi
            Bosnian 	                        bs
            Breton 	                            br
            Bulgarian 	                        bg
            Burmese 	                        my
            Catalan, Valencian 	                ca
            Chamorro 	                        ch
            Caucasian 	                        ce
            Chichewa, Chewa, Nyanja 	        ny 	nya 	nya 	nya 	
            Chinese 	                        zh 	zho 	chi 	zho + 16 	macrolanguage
            Chuvash 	                        cv 	chv 	chv 	chv 	
            Cornish 	                        kw 	cor 	cor 	cor 	
            Corsican 	                        co 	cos 	cos 	cos 	
            Cree 	                            cr 	cre 	cre 	cre + 6 	macrolanguage
            Croatian 	                        hr 	hrv 	hrv 	hrv 	
            Czech 	                            cs 	ces 	cze 	ces 	
            Danish 	                            da 	dan 	dan 	dan 	
            Divehi, Dhivehi, Maldivian          dv 	div 	div 	div 	
            Dutch, Flemish 	Nederlands, Vlaams 	nl 	nld 	dut 	nld 	Flemish is not to be confused with the closely related West Flemish which is referred to as Vlaams (Dutch for "Flemish") in ISO 639-3 and has the ISO 639-3 code vls
            Dzongkha                            dz
            English 	                        en 	eng 	eng 	eng 	
            Esperanto 	                        eo 	epo 	epo 	epo 	constructed, initiated from L.L. Zamenhof, 1887
            Estonian 	                        et 	est 	est 	est + 2 	macrolanguage
            Ewe 	                            ee 	ewe 	ewe 	ewe 	
            Faroese 	                        fo 	fao 	fao 	fao 	
            Fijian 	                            fj 	fij 	fij 	fij 	
            Finnish 	                        fi 	fin 	fin 	fin 	
            French 	français, langue française 	fr 	fra 	fre 	fra 	
            Fulah 	Fulfulde, Pulaar, Pular 	ff 	ful 	ful 	ful + 9 	macrolanguage, also known as Fula
            Galician 	                        gl 	glg 	glg 	glg 	
            Georgian 	                        ka 	kat 	geo 	kat 	
            German 	                            de 	deu 	ger 	deu 	
            Greek, Modern (1453–)               el 	ell 	gre 	ell 	
            Guarani 	                        gn 	grn 	grn 	grn + 5 	macrolanguage
            Gujarati 	                        gu 	guj 	guj 	guj 	
            Haitian, Haitian Creole 	        ht 	hat 	hat 	hat 	
            Hausa (Hausa) هَوُسَ ha 	hau 	hau 	hau 	
            Hebrew עברית he 	heb 	heb 	heb 	Modern Hebrew. Code changed in 1989 from original ISO 639:1988, iw.[1]
            Herero 	Otjiherero 	hz 	her 	her 	her 	
            Hindi 	हिन्दी, हिंदी 	hi 	hin 	hin 	hin 	
            Hiri Motu 	Hiri Motu 	ho 	hmo 	hmo 	hmo 	
            Hungarian 	magyar 	hu 	hun 	hun 	hun 	
            Interlingua (International Auxiliary Language Association) 	Interlingua 	ia 	ina 	ina 	ina 	constructed by International Auxiliary Language Association
            Indonesian 	Bahasa Indonesia 	id 	ind 	ind 	ind 	Covered by macrolanguage [ms/msa]. Changed in 1989 from original ISO 639:1988, in.[1]
            Interlingue, Occidental 	(originally:) Occidental, (after WWII:) Interlingue 	ie 	ile 	ile 	ile 	constructed by Edgar de Wahl, first published in 1922
            Irish 	Gaeilge 	ga 	gle 	gle 	gle 	
            Igbo 	Asụsụ Igbo 	ig 	ibo 	ibo 	ibo 	
            Inupiaq 	Iñupiaq, Iñupiatun 	ik 	ipk 	ipk 	ipk + 2 	macrolanguage
            Ido 	Ido 	io 	ido 	ido 	ido 	constructed by De Beaufront, 1907, as variation of Esperanto
            Icelandic 	Íslenska 	is 	isl 	ice 	isl 	
            Italian 	Italiano 	it 	ita 	ita 	ita 	
            Inuktitut 	ᐃᓄᒃᑎᑐᑦ 	iu 	iku 	iku 	iku + 2 	macrolanguage
            Japanese 	日本語 (にほんご) 	ja 	jpn 	jpn 	jpn 	
            Javanese 	ꦧꦱꦗꦮ, Basa Jawa 	jv 	jav 	jav 	jav 	
            Kalaallisut, Greenlandic 	kalaallisut, kalaallit oqaasii 	kl 	kal 	kal 	kal 	
            Kannada 	ಕನ್ನಡ 	kn 	kan 	kan 	kan 	
            Kanuri 	Kanuri 	kr 	kau 	kau 	kau + 3 	macrolanguage
            Kashmiri 	कश्मीरी, كشميري‎ 	ks 	kas 	kas 	kas 	
            h 	қазақ тілі 	kk 	kaz 	kaz 	kaz 	
            Central Khmer 	ខ្មែរ, ខេមរភាសា, ភាសាខ្មែរ 	km 	khm 	khm 	khm 	also known as Khmer or Cambodian
            Kikuyu, Gikuyu 	Gĩkũyũ 	ki 	kik 	kik 	kik 	
            Kinyarwanda 	Ikinyarwanda 	rw 	kin 	kin 	kin 	
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
            Zhuang, Chuang 	Saɯ cueŋƅ, Saw cuengh 	za 	zha 	zha 	zha + 16 	macrolanguage
            Zulu 	isiZulu 	zu 	zul 	zul 	zul 	
        ];

        $index = 1;
        $data = array_map(
            function ($item) use (&$index) {
                return array_merge($item, [
                    'id' => $index++,
                    'slug' => Str::slug($item['name']),
                ]);
            },
            $data
        );
        Category::insert($data);

        /**
         * Sub categories
         */

        $artsId = Category::bySlug('arts')->id;
        $businessId = Category::bySlug('business')->id;
        $comedyId = Category::bySlug('comedy')->id;
        $educationId = Category::bySlug('education')->id;
        $fictionId = Category::bySlug('fiction')->id;
        $healthFitnessId = Category::bySlug('health-fitness')->id;
        $kidsFamilyId = Category::bySlug('kids-family')->id;
        $leisureId = Category::bySlug('leisure')->id;
        $musicId = Category::bySlug('music')->id;
        $newsId = Category::bySlug('news')->id;
        $religionSpiritualityId = Category::bySlug('religion-spirituality')->id;
        $scienceId = Category::bySlug('science')->id;
        $societyCultureId = Category::bySlug('society-culture')->id;
        $sportsId = Category::bySlug('sports')->id;
        $tvFilmId = Category::bySlug('tv-film')->id;

        $data = [
            /**
             * Arts categories
             */
            ['parent_id' => $artsId, 'name' => 'Books'],
            ['parent_id' => $artsId, 'name' => 'Design'],
            ['parent_id' => $artsId, 'name' => 'Fashion & Beauty'],
            ['parent_id' => $artsId, 'name' => 'Food'],
            ['parent_id' => $artsId, 'name' => 'Performing Arts'],
            ['parent_id' => $artsId, 'name' => 'Visual Arts'],
            /**
             * Business categories
             */
            ['parent_id' => $businessId, 'name' => 'Careers'],
            ['parent_id' => $businessId, 'name' => 'Entrepreneurship'],
            ['parent_id' => $businessId, 'name' => 'Investing'],
            ['parent_id' => $businessId, 'name' => 'Management'],
            ['parent_id' => $businessId, 'name' => 'Marketing'],
            ['parent_id' => $businessId, 'name' => 'NonProfit'],
            /**
             * Comedy categories
             */
            ['parent_id' => $comedyId, 'name' => 'Comedy Interviews'],
            ['parent_id' => $comedyId, 'name' => 'Improv'],
            ['parent_id' => $comedyId, 'name' => 'StandUp'],
            /**
             * Education categories
             */
            ['parent_id' => $educationId, 'name' => 'Courses'],
            ['parent_id' => $educationId, 'name' => 'How To'],
            ['parent_id' => $educationId, 'name' => 'Language Learning'],
            ['parent_id' => $educationId, 'name' => 'Self-Improvement'],
            /**
             * Fiction categories
             */
            ['parent_id' => $fictionId, 'name' => 'Comedy Fiction'],
            ['parent_id' => $fictionId, 'name' => 'Drama'],
            ['parent_id' => $fictionId, 'name' => 'Science Fiction'],
            /**
             * Health & Fitness categories
             */
            ['parent_id' => $healthFitnessId, 'name' => 'Alternative Health'],
            ['parent_id' => $healthFitnessId, 'name' => 'Fitness'],
            ['parent_id' => $healthFitnessId, 'name' => 'Medicine'],
            ['parent_id' => $healthFitnessId, 'name' => 'Mental Health'],
            ['parent_id' => $healthFitnessId, 'name' => 'Nutrition'],
            ['parent_id' => $healthFitnessId, 'name' => 'Sexuality'],
            /**
             * Kids & Family categories
             */
            ['parent_id' => $kidsFamilyId, 'name' => 'Education for Kids'],
            ['parent_id' => $kidsFamilyId, 'name' => 'Parenting'],
            ['parent_id' => $kidsFamilyId, 'name' => 'Pets & Animals'],
            ['parent_id' => $kidsFamilyId, 'name' => 'Stories For Kids'],
            /**
             * Leisure categories
             */
            ['parent_id' => $leisureId, 'name' => 'Animation Manga'],
            ['parent_id' => $leisureId, 'name' => 'Automotive'],
            ['parent_id' => $leisureId, 'name' => 'Aviation'],
            ['parent_id' => $leisureId, 'name' => 'Crafts'],
            ['parent_id' => $leisureId, 'name' => 'Games'],
            ['parent_id' => $leisureId, 'name' => 'Hobbies'],
            ['parent_id' => $leisureId, 'name' => 'Home & Garden'],
            ['parent_id' => $leisureId, 'name' => 'Video Games'],
            /**
             * Music categories
             */
            ['parent_id' => $musicId, 'name' => 'Music Commentary'],
            ['parent_id' => $musicId, 'name' => 'Music History'],
            ['parent_id' => $musicId, 'name' => 'Music Interviews'],
            /**
             * News categories
             */
            ['parent_id' => $newsId, 'name' => 'Business News'],
            ['parent_id' => $newsId, 'name' => 'Daily News'],
            ['parent_id' => $newsId, 'name' => 'Entertainment News'],
            ['parent_id' => $newsId, 'name' => 'News Commentary'],
            ['parent_id' => $newsId, 'name' => 'Politics'],
            ['parent_id' => $newsId, 'name' => 'Sports News'],
            ['parent_id' => $newsId, 'name' => 'Tech News'],

            /**
             * Religion & Spirtuality categories
             */
            ['parent_id' => $religionSpiritualityId, 'name' => 'Buddhism'],
            ['parent_id' => $religionSpiritualityId, 'name' => 'Christianity'],
            ['parent_id' => $religionSpiritualityId, 'name' => 'Hinduism'],
            ['parent_id' => $religionSpiritualityId, 'name' => 'Islam'],
            ['parent_id' => $religionSpiritualityId, 'name' => 'Judaism'],
            ['parent_id' => $religionSpiritualityId, 'name' => 'Religion'],
            ['parent_id' => $religionSpiritualityId, 'name' => 'Spirituality'],
            /**
             * Science categories
             */
            ['parent_id' => $scienceId, 'name' => 'Astronomy'],
            ['parent_id' => $scienceId, 'name' => 'Chemistry'],
            ['parent_id' => $scienceId, 'name' => 'Earth Sciences'],
            ['parent_id' => $scienceId, 'name' => 'Life Sciences'],
            ['parent_id' => $scienceId, 'name' => 'Mathematics'],
            ['parent_id' => $scienceId, 'name' => 'Natural Sciences'],
            ['parent_id' => $scienceId, 'name' => 'Nature'],
            ['parent_id' => $scienceId, 'name' => 'Physics'],
            ['parent_id' => $scienceId, 'name' => 'Social Sciences'],
            /**
             * Society & Culture categories
             */
            ['parent_id' => $societyCultureId, 'name' => 'Documentary'],
            ['parent_id' => $societyCultureId, 'name' => 'Personal Journals'],
            ['parent_id' => $societyCultureId, 'name' => 'Philosophy'],
            ['parent_id' => $societyCultureId, 'name' => 'Places & Travel'],
            ['parent_id' => $societyCultureId, 'name' => 'Relationships'],
            /**
             * Sports categories
             */
            ['parent_id' => $sportsId, 'name' => 'Baseball'],
            ['parent_id' => $sportsId, 'name' => 'Basketball'],
            ['parent_id' => $sportsId, 'name' => 'Cricket'],
            ['parent_id' => $sportsId, 'name' => 'Fantasy Sports'],
            ['parent_id' => $sportsId, 'name' => 'Football'],
            ['parent_id' => $sportsId, 'name' => 'Golf'],
            ['parent_id' => $sportsId, 'name' => 'Hockey'],
            ['parent_id' => $sportsId, 'name' => 'Rugby'],
            ['parent_id' => $sportsId, 'name' => 'Running'],
            ['parent_id' => $sportsId, 'name' => 'Soccer'],
            ['parent_id' => $sportsId, 'name' => 'Swimming'],
            ['parent_id' => $sportsId, 'name' => 'Tennis'],
            ['parent_id' => $sportsId, 'name' => 'Volleyball'],
            ['parent_id' => $sportsId, 'name' => 'Wilderness'],
            ['parent_id' => $sportsId, 'name' => 'Wrestling'],
            /**
             * TV & Film categories
             */
            ['parent_id' => $tvFilmId, 'name' => 'After Shows'],
            ['parent_id' => $tvFilmId, 'name' => 'Film History'],
            ['parent_id' => $tvFilmId, 'name' => 'Film Interviews'],
            ['parent_id' => $tvFilmId, 'name' => 'Film Reviews'],
            ['parent_id' => $tvFilmId, 'name' => 'Tv Reviews'],
        ];
        $data = array_map(
            function ($item) use (&$index) {
                return array_merge($item, [
                    'id' => $index++,
                    'slug' => Str::slug($item['name']),
                ]);
            },
            $data
        );
        Category::insert($data);
    }
}
