<?php
/**
 * Internationalisation file for extension OggPlayer.
 *
 * @file
 * @ingroup Extensions
 */

$messages = array();

$messages['en'] = array(
	'timedmedia-desc'             => 'Handler for audio, video and timed text, with format support for WebM, Ogg Theora, Vorbis, srt',
	'timedmedia-ogg-short-audio'  => 'Ogg $1 sound file, $2',
	'timedmedia-ogg-short-video'      => 'Ogg $1 video file, $2',
	'timedmedia-ogg-short-general'    => 'Ogg $1 media file, $2',
	'timedmedia-ogg-long-audio'       => 'Ogg $1 sound file, length $2, $3',
	'timedmedia-ogg-long-video'       => 'Ogg $1 video file, length $2, $4 × $5 pixels, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg multiplexed audio/video file, $1, length $2, $4 × $5 pixels, $3 overall',
	'timedmedia-ogg-long-general'     => 'Ogg media file, length $2, $3',
	'timedmedia-ogg-long-error'       => 'Invalid Ogg file: $1',

	'timedmedia-webm-short-video' => 'WebM $1 video file, $2',
	'timedmedia-webm-long-video' => 'WebM audio/video file, $1, length $2, $4 × $5 pixels, $3 overall',
	
	'timedmedia-no-player-js' 	  => 'Sorry, your browser either has JavaScript disabled or does not have any supported player.<br />
You can <a href="$1">download the clip</a> or <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">download a player</a> to play the clip in your browser.',

	'timedmedia-more'             => 'More…',
	'timedmedia-dismiss'          => 'Close',
	'timedmedia-download'         => 'Download file',
	'timedmedia-play-media'		  => 'Play media',
	'timedmedia-desc-link'        => 'About this file',
	'timedmedia-oggThumb-version' => 'OggHandler requires oggThumb version $1 or later.',
	'timedmedia-oggThumb-failed'  => 'oggThumb failed to create the thumbnail.',

	// Original uploaded asset
	'timedmedia-ogg' => 'Ogg',
	'timedmedia-webm' => 'WebM',
	'timedmedia-source-file' => '$1 source',
	'timedmedia-source-file-desc' => 'Original $1, $2 × $3 ($4)',

	// derivative timedmedia-derivative-desc-220_200kbs.ogv
	'timedmedia-derivative-220_200kbs.ogv' => 'Ogg 200P',
	'timedmedia-derivative-desc-220_200kbs.ogv' => 'Low bandwidth Ogg video (200P)',

	'timedmedia-derivative-360_400kbs.ogv' => 'Ogg 360P',
	'timedmedia-derivative-desc-360_400kbs.ogv' => 'Web streamable Ogg video (360P)',
	
	'timedmedia-derivative-480_600kbs.ogv' => 'Ogg 480P',
	'timedmedia-derivative-desc-480_600kbs.ogv' => 'Web streamable Ogg video (480P)',

	'timedmedia-derivative-720_VBR.ogv' => 'Ogg HQ',
	'timedmedia-derivative-desc-720_VBR.ogv' => 'High quality downloadable Ogg video (720P)',
	
	// WebM profiles: 	
	'timedmedia-derivative-480_600kbs.webm' => 'WebM 480P',
	'timedmedia-derivative-desc-480_600kbs.webm' => 'Web streamable WebM (480P)',
	'timedmedia-derivative-720_VBR.webm' => 'WebM 720P',
	'timedmedia-derivative-desc720_VBR.webm' => 'High quality downloadable WebM (720P)',

	'timedmedia-subtitle-language' => '$1 ($2) subtitles',
);

/** Message documentation (Message documentation)
 * @author Aotake
 * @author BrokenArrow
 * @author EugeneZelenko
 * @author Fryed-peach
 * @author Jon Harald Søby
 * @author Meno25
 * @author Mormegil
 * @author Purodha
 * @author Raymond
 * @author Siebrand
 */
$messages['qqq'] = array(
	'timedmedia-desc' => '{{desc}}',
	'timedmedia-ogg-short-general' => 'File details for generic (non-audio, non-video) Ogg files, short version.
Parameters are:
* $1 file type, e.g. Vorbis, Speex
* $2 ?',
	'timedmedia-ogg-long-audio' => 'File details for Ogg files, shown after the filename in the image description page.
Parameters are:
* $1 file codec, f.e. Vorbis, Speex
* $2 file duration, f.e. 1m34s
* $3 file sampling rate, f.e. 97kbps',
	'timedmedia-webm-short-video' => 'Parameters:
* $1 is a slash ("/") separated list of stream types.
* $2 is a time period.',
	'timedmedia-more' => '{{Identical|More...}}',
	'timedmedia-dismiss' => '{{Identical|Close}}',
	'timedmedia-download' => '{{Identical|Download}}',
	'timedmedia-source-file' => 'The source file 
* $1 file type webm or ogg
{{Identical|Source}}',
	'timedmedia-source-file-desc' => 'Source file description. Paramaters:
* $1 file type ie webm or ogg
* $2 resolution width
* $3 resolution height
* $4 human readable bitrate',
	'timedmedia-subtitle-language' => 'Subtitle names
Paramaters are:
* $1 Subtitle language 
* $2 Subtitle key',
);

/** Albaamo innaaɬiilka (Albaamo innaaɬiilka)
 * @author Ulohnanne
 */
$messages['akz'] = array(
	'timedmedia-more' => 'Maatàasasi...',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 * @author SPQRobin
 */
$messages['af'] = array(
	'timedmedia-desc' => "Hanteer Ogg Theora- en Vorbis-lêers met 'n JavaScript-mediaspeler",
	'timedmedia-ogg-short-audio' => 'Ogg $1 klanklêer, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 video lêer, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 medialêer, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 klanklêer, lengte $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 videolêer, lengte $2, $4×$5 pixels, $3',
	'timedmedia-ogg-long-general' => 'Ogg medialêer, lengte $2, $3',
	'timedmedia-ogg-long-error' => 'Ongeldige Ogg-lêer: $1',
	'timedmedia-more' => 'Meer…',
	'timedmedia-dismiss' => 'Sluit',
	'timedmedia-download' => 'Laai lêer af',
	'timedmedia-desc-link' => 'Aangaande die lêer',
);

/** Gheg Albanian (Gegë)
 * @author Mdupont
 */
$messages['aln'] = array(
	'timedmedia-desc' => 'Mbajtës për mediat në kohën e duhur (video, audio, timedText) me transcoding të ZQM Theora / Vorbis',
	'timedmedia-ogg-short-audio' => 'Ogg tingull $1 fotografi, $2',
	'timedmedia-ogg-short-video' => 'video file Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 media file, $2',
	'timedmedia-ogg-long-audio' => 'ZQM file $1 shëndoshë, gjatë $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 video file, gjatë $2, $4 × $5 pixels, $3',
	'timedmedia-ogg-long-multiplexed' => 'ZQM multiplexed audio / video file, $1, gjatë $2, $4 × $5 pixels, $3 e përgjithshme',
	'timedmedia-ogg-long-general' => 'Ogg media file, gjatë $2, $3',
	'timedmedia-ogg-long-error' => 'E pavlefshme Ogg file: $1',
	'timedmedia-no-player-js' => 'Na vjen keq, browser-i juaj ose ka JavaScript paaftë ose nuk ka asnjë lojtar të mbështetur. <br /> Ju mund të <a href="$1">shkarkoni clip</a> ose <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">shkarkoni një lojtar</a> për të luajtur clip në shfletuesin tuaj.',
	'timedmedia-more' => 'Më shumë ...',
	'timedmedia-dismiss' => 'Afër',
	'timedmedia-download' => 'Shkarko file',
	'timedmedia-desc-link' => 'Për këtë fotografi',
	'timedmedia-oggThumb-version' => 'OggHandler kërkon version oggThumb $1 ose më vonë.',
	'timedmedia-oggThumb-failed' => 'oggThumb dështuar për të krijuar tablo.',
);

/** Aragonese (Aragonés)
 * @author Juanpabl
 */
$messages['an'] = array(
	'timedmedia-desc' => 'Maneyador de fichers sincronizatos (vidio, son y texto sincronizato) con transcodificación Ogg Theora/Vorbis',
	'timedmedia-ogg-short-audio' => 'Fichero de son ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Fichero de vidio ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Fichero multimedia ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'Fichero de son ogg $1, durada $2, $3',
	'timedmedia-ogg-long-video' => 'Fichero de vidio ogg $1, durada $2, $4×$5 píxels, $3',
	'timedmedia-ogg-long-multiplexed' => 'fichero ogg multiplexato audio/vidio, $1, durada $2, $4×$5 píxels, $3 total',
	'timedmedia-ogg-long-general' => 'fichero ogg multimedia durada $2, $3',
	'timedmedia-ogg-long-error' => 'Fichero ogg no conforme: $1',
	'timedmedia-more' => 'Más…',
	'timedmedia-dismiss' => 'Zarrar',
	'timedmedia-download' => 'Escargar fichero',
	'timedmedia-desc-link' => 'Información sobre este fichero',
);

/** Arabic (العربية)
 * @author Alnokta
 * @author Meno25
 * @author OsamaK
 */
$messages['ar'] = array(
	'timedmedia-desc' => 'متحكم لملفات Ogg Theora وVorbis، مع لاعب جافاسكريت',
	'timedmedia-ogg-short-audio' => 'Ogg $1 ملف صوت، $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 ملف فيديو، $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 ملف ميديا، $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 ملف صوت، الطول $2، $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 ملف فيديو، الطول $2، $4×$5 بكسل، $3',
	'timedmedia-ogg-long-multiplexed' => 'ملف Ogg مالتي بليكسد أوديو/فيديو، $1، الطول $2، $4×$5 بكسل، $3 إجمالي',
	'timedmedia-ogg-long-general' => 'ملف ميديا Ogg، الطول $2، $3',
	'timedmedia-ogg-long-error' => 'ملف Ogg غير صحيح: $1',
	'timedmedia-more' => 'المزيد...',
	'timedmedia-dismiss' => 'إغلاق',
	'timedmedia-download' => 'نزل الملف',
	'timedmedia-desc-link' => 'عن هذا الملف',
);

/** Aramaic (ܐܪܡܝܐ)
 * @author Basharh
 */
$messages['arc'] = array(
	'timedmedia-more' => 'ܝܬܝܪ…',
);

/** Egyptian Spoken Arabic (مصرى)
 * @author Ghaly
 * @author Meno25
 * @author Ramsis II
 */
$messages['arz'] = array(
	'timedmedia-desc' => 'متحكم لملفات أو جى جى ثيورا و فوربيس، مع بلاير جافاسكريبت',
	'timedmedia-ogg-short-audio' => 'Ogg $1 ملف صوت، $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 ملف فيديو, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 ملف ميديا، $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 ملف صوت، الطول $2، $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 ملف فيديو، الطول $2، $4×$5 بكسل، $3',
	'timedmedia-ogg-long-multiplexed' => 'ملف Ogg مالتى بليكسد أوديو/فيديو، $1، الطول $2، $4×$5 بكسل، $3 إجمالي',
	'timedmedia-ogg-long-general' => 'ملف ميديا Ogg، الطول $2، $3',
	'timedmedia-ogg-long-error' => 'ملف ogg مش صحيح: $1',
	'timedmedia-more' => 'أكتر...',
	'timedmedia-dismiss' => 'اقفل',
	'timedmedia-download' => 'نزل الملف',
	'timedmedia-desc-link' => 'عن الملف دا',
);

/** Asturian (Asturianu)
 * @author Esbardu
 */
$messages['ast'] = array(
	'timedmedia-desc' => "Remanador d'archivos Ogg Theora y Vorbis, con un reproductor JavaScript",
	'timedmedia-ogg-short-audio' => 'Archivu de soníu ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Archivu de videu ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Archivu multimedia ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'Archivu de soníu ogg $1, llonxitú $2, $3',
	'timedmedia-ogg-long-video' => 'Archivu de videu ogg $1, llonxitú $2, $4×$5 píxeles, $3',
	'timedmedia-ogg-long-multiplexed' => "Archivu d'audiu/videu ogg multiplexáu, $1, llonxitú $2, $4×$5 píxeles, $3",
	'timedmedia-ogg-long-general' => 'Archivu multimedia ogg, llonxitú $2, $3',
	'timedmedia-ogg-long-error' => 'Archivu ogg non válidu: $1',
	'timedmedia-more' => 'Más...',
	'timedmedia-dismiss' => 'Zarrar',
	'timedmedia-download' => 'Descargar archivu',
	'timedmedia-desc-link' => 'Tocante a esti archivu',
);

/** Kotava (Kotava)
 * @author Sab
 */
$messages['avk'] = array(
	'timedmedia-download' => 'Iyeltakkalvajara',
	'timedmedia-desc-link' => 'Icde bat iyeltak',
);

/** Southern Balochi (بلوچی مکرانی)
 * @author Mostafadaneshvar
 */
$messages['bcc'] = array(
	'timedmedia-desc' => 'دسگیره په فایلان Ogg Theora و Vorbis, گون پخش کنوک جاوا اسکرسیپت',
	'timedmedia-ogg-short-audio' => 'فایل صوتی Ogg $1، $2',
	'timedmedia-ogg-short-video' => 'فایل تصویری Ogg $1، $2',
	'timedmedia-ogg-short-general' => 'فایل مدیا Ogg $1، $2',
	'timedmedia-ogg-long-audio' => 'اوجی جی  $1 فایل صوتی, طول $2, $3',
	'timedmedia-ogg-long-video' => 'اوجی جی $1 فایل ویدیو, طول $2, $4×$5 پیکسل, $3',
	'timedmedia-ogg-long-multiplexed' => 'اوجی جی چند دابی فایل صوت/تصویر, $1, طول $2, $4×$5 پیکسل, $3 کل',
	'timedmedia-ogg-long-general' => 'اوجی جی فایل مدیا, طول $2, $3',
	'timedmedia-ogg-long-error' => 'نامعتبرین فایل اوجی جی: $1',
	'timedmedia-more' => 'گیشتر...',
	'timedmedia-dismiss' => 'بندگ',
	'timedmedia-download' => 'ایرگیزگ فایل',
	'timedmedia-desc-link' => 'ای فایل باره',
);

/** Bikol Central (Bikol Central)
 * @author Filipinayzd
 */
$messages['bcl'] = array(
	'timedmedia-more' => 'Dakol pa..',
	'timedmedia-dismiss' => 'Isara',
);

/** Belarusian (Taraškievica orthography) (‪Беларуская (тарашкевіца)‬)
 * @author EugeneZelenko
 * @author Jim-by
 * @author Red Winged Duck
 * @author Wizardist
 */
$messages['be-tarask'] = array(
	'timedmedia-desc' => 'Апрацоўшчык аўдыё, відэа і субтытраў у фарматах WebM, Ogg Theora, Vorbis і SRT',
	'timedmedia-ogg-short-audio' => 'Аўдыё-файл Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Відэа-файл у фармаце Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Мэдыя-файл Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'аўдыё-файл Ogg $1, даўжыня $2, $3',
	'timedmedia-ogg-long-video' => 'відэа-файл Ogg $1, даўжыня $2, $4×$5 піксэляў, $3',
	'timedmedia-ogg-long-multiplexed' => 'мультыплексны аўдыё/відэа-файл Ogg, $1, даўжыня $2, $4×$5 піксэляў, усяго $3',
	'timedmedia-ogg-long-general' => 'мэдыя-файл Ogg, даўжыня $2, $3',
	'timedmedia-ogg-long-error' => 'Няслушны файл у фармаце Ogg: $1',
	'timedmedia-no-player-js' => 'Прабачце, але ў Вашым браўзэры адключаны JavaScript альбо няма неабходнага прайгравальніка.<br />
Вы можаце <a href="$1">загрузіць кліп</a> ці <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">загрузіць прайгравальнік</a> для прайграваньня кліпу ў Вашым браўзэры.',
	'timedmedia-more' => 'Болей…',
	'timedmedia-dismiss' => 'Зачыніць',
	'timedmedia-download' => 'Загрузіць файл',
	'timedmedia-desc-link' => 'Інфармацыя пра гэты файл',
	'timedmedia-oggThumb-version' => 'OggHandler патрабуе oggThumb вэрсіі $1 ці больш позьняй.',
	'timedmedia-oggThumb-failed' => 'oggThumb не атрымалася стварыць мініятуру.',
	'timedmedia-source-file' => 'Крыніца $1',
	'timedmedia-derivative-220_200kbs.ogv' => 'Ogg 200P',
	'timedmedia-derivative-desc-220_200kbs.ogv' => 'Нізкапаточнае Ogg-відэа (200P)',
	'timedmedia-derivative-desc-360_400kbs.ogv' => 'Паточнае Ogg-відэа (360 пкс)',
	'timedmedia-derivative-desc-480_600kbs.ogv' => 'Паточнае Ogg-відэа (480 пкс)',
	'timedmedia-derivative-desc-720_VBR.ogv' => 'Высакаякаснае Ogg-відэа для загрузкі (720 пкс)',
	'timedmedia-subtitle-language' => 'Субтытры ($2) на $1',
);

/** Bulgarian (Български)
 * @author Borislav
 * @author DCLXVI
 * @author Spiritia
 */
$messages['bg'] = array(
	'timedmedia-desc' => 'Приложение за файлове тип Ogg Theora и Vorbis, с плейър на JavaScript',
	'timedmedia-ogg-short-audio' => 'Ogg $1 звуков файл, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 видео файл, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 звуков файл, продължителност $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 видео файл, продължителност $2, $4×$5 пиксела, $3',
	'timedmedia-ogg-long-general' => 'Мултимедиен файл в ogg формат с дължина $2, $3',
	'timedmedia-ogg-long-error' => 'Невалиден ogg файл: $1',
	'timedmedia-more' => 'Повече...',
	'timedmedia-dismiss' => 'Затваряне',
	'timedmedia-download' => 'Изтегляне на файла',
	'timedmedia-desc-link' => 'Информация за файла',
);

/** Bengali (বাংলা)
 * @author Bellayet
 * @author Zaheen
 */
$messages['bn'] = array(
	'timedmedia-ogg-short-audio' => 'অগ $1 সাউন্ড ফাইল, $2',
	'timedmedia-ogg-short-video' => 'অগ $1 ভিডিও ফাইল, $2',
	'timedmedia-ogg-short-general' => 'অগ $1 মিডিয়া ফাইল, $2',
	'timedmedia-ogg-long-audio' => 'অগ $1 সাউন্ড ফাইল, দৈর্ঘ্য $2, $3',
	'timedmedia-ogg-long-video' => 'অগ $1 ভিডিও ফাইল, দৈর্ঘ্য $2, $4×$5 পিক্সেল, $3',
	'timedmedia-ogg-long-multiplexed' => 'অগ মাল্টিপ্লেক্সকৃত অডিও/ভিডিও ফাইল, $1, দৈর্ঘ্য $2, $4×$5 পিক্সেল, $3 সামগ্রিক',
	'timedmedia-ogg-long-general' => 'অগ মিডিয়া ফাইল, দৈর্ঘ্য $2, $3',
	'timedmedia-ogg-long-error' => 'অবৈধ অগ ফাইল: $1',
	'timedmedia-more' => 'আরও...',
	'timedmedia-dismiss' => 'বন্ধ করা হোক',
	'timedmedia-download' => 'ফাইল ডাউনলোড করুন',
	'timedmedia-desc-link' => 'এই ফাইলের বৃত্তান্ত',
);

/** Breton (Brezhoneg)
 * @author Fohanno
 * @author Fulup
 * @author Y-M D
 */
$messages['br'] = array(
	'timedmedia-desc' => 'Benveg kontrolliñ elfennoù liesmedia sinkronek (video, son, ha testenn sinkronekaet) gant treuzkodiñ da Ogg Theora/Vorbis',
	'timedmedia-ogg-short-audio' => 'Restr son Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Restr video Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Restr media Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'Restr son Ogg $1, pad $2, $3',
	'timedmedia-ogg-long-video' => 'Restr video Ogg $1, pad $2, $4×$5 piksel, $3',
	'timedmedia-ogg-long-multiplexed' => 'Restr Ogg klevet/video liesplezhet $1, pad $2, $4×$5 piksel, $3 hollad',
	'timedmedia-ogg-long-general' => 'Restr media Ogg, pad $2, $3',
	'timedmedia-ogg-long-error' => 'Restr ogg direizh : $1',
	'timedmedia-webm-short-video' => 'Restr video WebM $1, $2',
	'timedmedia-no-player-js' => 'Ho tigarez, pe eo diweredekaet JavaScript war ho merdeer pen n\'eo ket skoret lenner ebet gantañ.<br />
<a href="$1">Pellgargañ ar c\'hlip</a> a c\'hallit pe <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">pellgargañ ul lenner</a> da lenn ar c\'hlip gant ho merdeer.',
	'timedmedia-more' => "Muioc'h...",
	'timedmedia-dismiss' => 'Serriñ',
	'timedmedia-download' => 'Pellgargañ ar restr',
	'timedmedia-desc-link' => 'Diwar-benn ar restr-mañ',
	'timedmedia-oggThumb-version' => "Rekis eo stumm $1 oggThumb, pe nevesoc'h, evit implijout OggHandler.",
	'timedmedia-oggThumb-failed' => "N'eo ket deuet a-benn oggThumb da grouiñ ar munud.",
	'timedmedia-subtitle-language' => 'istitloù e $1 ($2)',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'timedmedia-desc' => 'Upravljač za zvuk, video i vremenski tekst sa podrškom formata za WebM, Ogg Theora, Vorbis, srt',
	'timedmedia-ogg-short-audio' => 'Ogg $1 zvučna datoteka, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 video datoteka, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 medijalna datoteka, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 zvučna datoteka, dužina $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 video datoteka, dužina $2, $4×$5 piksela, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg multipleksna zvučna/video datoteka, $1, dužina $2, $4×$5 piksela, $3 sveukupno',
	'timedmedia-ogg-long-general' => 'Ogg medijalna datoteka, dužina $2, $3',
	'timedmedia-ogg-long-error' => 'Nevaljana ogg datoteka: $1',
	'timedmedia-no-player-js' => 'Žao nam je, vaš preglednik ili je onemogućio JavaScript ili nema nijednog podržanog playera.<br />
Možete <a href="$1">učitati klip</a> ili <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">učitati player</a> za reproduciranje klipa u vašem pregledniku.',
	'timedmedia-more' => 'Više...',
	'timedmedia-dismiss' => 'Zatvori',
	'timedmedia-download' => 'Učitaj datoteku',
	'timedmedia-desc-link' => 'O ovoj datoteci',
	'timedmedia-oggThumb-version' => 'OggHandler zahtijeva oggThumb verziju $1 ili kasniju.',
	'timedmedia-oggThumb-failed' => 'oggThumb nije uspio napraviti smanjeni pregled.',
	'timedmedia-source-file' => '$1 izvor',
	'timedmedia-source-file-desc' => 'Original $1, $2 × $3 ($4)',
	'timedmedia-derivative-220_200kbs.ogv' => 'Ogg 200P',
	'timedmedia-subtitle-language' => '$1 ($2) podnaslovi',
);

/** Catalan (Català)
 * @author Aleator
 * @author Paucabot
 * @author SMP
 * @author Toniher
 * @author Vriullop
 */
$messages['ca'] = array(
	'timedmedia-desc' => 'Gestor de fitxers Ogg Theora i Vorbis, amb reproductor de Javascript',
	'timedmedia-ogg-short-audio' => "Fitxer OGG d'àudio $1, $2",
	'timedmedia-ogg-short-video' => 'Fitxer OGG de vídeo $1, $2',
	'timedmedia-ogg-short-general' => 'Fitxer multimèdia OGG $1, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 fitxer de so, llargada $2, $3',
	'timedmedia-ogg-long-video' => 'Fitxer OGG de vídeo $1, llargada $2, $4×$5 píxels, $3',
	'timedmedia-ogg-long-multiplexed' => 'Arxiu àudio/vídeo multiplex, $1, llargada $2, $4×$5 píxels, $3 de mitjana',
	'timedmedia-ogg-long-general' => 'Fitxer multimèdia OGG, llargada $2, $3',
	'timedmedia-ogg-long-error' => 'Fitxer OGG invàlid: $1',
	'timedmedia-more' => 'Més...',
	'timedmedia-dismiss' => 'Tanca',
	'timedmedia-download' => 'Descarrega el fitxer',
	'timedmedia-desc-link' => 'Informació del fitxer',
);

/** Chechen (Нохчийн)
 * @author Sasan700
 */
$messages['ce'] = array(
	'timedmedia-download' => 'Чуйаккха хlум',
);

/** Czech (Česky)
 * @author Li-sung
 * @author Matěj Grabovský
 * @author Mormegil
 */
$messages['cs'] = array(
	'timedmedia-desc' => 'Obsluha časovaných souborů (video, audio, titulky) s převodem do Ogg Theora/Vorbis',
	'timedmedia-ogg-short-audio' => 'Zvukový soubor ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Videosoubor ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Multimediální soubor ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'Zvukový soubor ogg $1, délka $2, $3',
	'timedmedia-ogg-long-video' => 'Videosoubor $1, délka $2, $4×$5 pixelů, $3',
	'timedmedia-ogg-long-multiplexed' => 'Audio/video soubor ogg, $1, délka $2, $4×$5 pixelů, $3',
	'timedmedia-ogg-long-general' => 'Soubor média ogg, délka $2, $3',
	'timedmedia-ogg-long-error' => 'Chybný soubor ogg: $1',
	'timedmedia-no-player-js' => 'Je mi líto, ale váš prohlížeč má buď vypnutý JavaScript, nebo nemáte žádný podporovaný přehrávač.<br />
Můžete si <a href="$1">stáhnout klip</a> nebo si <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/cs">stáhnout přehrávač</a>, kterým si klip přehrajete v prohlížeči.',
	'timedmedia-more' => 'Více...',
	'timedmedia-dismiss' => 'Zavřít',
	'timedmedia-download' => 'Stáhnout soubor',
	'timedmedia-desc-link' => 'O tomto souboru',
	'timedmedia-oggThumb-version' => 'OggHandler vyžaduje oggThumb verze $1 nebo novější.',
	'timedmedia-oggThumb-failed' => 'oggThumb nedokázal vytvořit náhled.',
);

/** Danish (Dansk)
 * @author Byrial
 * @author Jon Harald Søby
 */
$messages['da'] = array(
	'timedmedia-desc' => 'Understøtter Ogg Theora- og Vorbis-filer med en JavaScript-afspiller.',
	'timedmedia-ogg-short-audio' => 'Ogg $1 lydfil, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 videofil, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 mediafil, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 lydfil, længde $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 videofil, længde $2, $4×$5 pixel, $3',
	'timedmedia-ogg-long-multiplexed' => 'Sammensat Ogg-lyd- og -videofil, $1, længde $2, $4×$5 pixel, $3 samlet',
	'timedmedia-ogg-long-general' => 'Ogg mediafil, længde $2, $3',
	'timedmedia-ogg-long-error' => 'Ugyldig Ogg-fil: $1',
	'timedmedia-more' => 'Mere...',
	'timedmedia-dismiss' => 'Luk',
	'timedmedia-download' => 'Download fil',
	'timedmedia-desc-link' => 'Om denne fil',
);

/** German (Deutsch)
 * @author Kghbln
 * @author Leithian
 * @author Metalhead64
 * @author MichaelFrey
 * @author Raimond Spekking
 * @author The Evil IP address
 * @author Umherirrender
 */
$messages['de'] = array(
	'timedmedia-desc' => 'Stellt ein Steuerungsprogramm für zeitgesteuerte Medien (Video, Audio, timedText) bereit, welches die Formate WebM, Ogg Theora, Ogg Vorbis und SubRip unterstützt',
	'timedmedia-ogg-short-audio' => 'Ogg-$1-Audiodatei, $2',
	'timedmedia-ogg-short-video' => 'Ogg-$1-Videodatei, $2',
	'timedmedia-ogg-short-general' => 'Ogg-$1-Mediadatei, $2',
	'timedmedia-ogg-long-audio' => 'Ogg-$1-Audiodatei, Länge: $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg-$1-Videodatei, Länge: $2, $4×$5 Pixel, $3 insgesamt',
	'timedmedia-ogg-long-multiplexed' => 'Ogg-Audio-/Video-Datei, $1, Länge: $2, $4×$5 Pixel, $3 insgesamt',
	'timedmedia-ogg-long-general' => 'Ogg-Mediadatei, Länge: $2, $3',
	'timedmedia-ogg-long-error' => 'Ungültige Ogg-Datei: $1',
	'timedmedia-webm-short-video' => 'WebM-$1-Videodatei, $2',
	'timedmedia-webm-long-video' => 'WebM-Audio-/Video-Datei, $1, Länge: $2, $4×$5 Pixel, $3 insgesamt',
	'timedmedia-no-player-js' => 'Entschuldige, aber dein Browser hat entweder JavaScript deaktiviert oder keine unterstützte Abspielsoftware.<br />
Du kannst <a href="$1">den Clip herunterladen</a> oder <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">eine Abspielsoftware herunterladen</a>, um den Clip im Browser abzuspielen.',
	'timedmedia-more' => 'Optionen …',
	'timedmedia-dismiss' => 'Schließen',
	'timedmedia-download' => 'Datei herunterladen',
	'timedmedia-play-media' => 'Mediendatei abspielen',
	'timedmedia-desc-link' => 'Über diese Datei',
	'timedmedia-oggThumb-version' => 'OggHandler erfordert oggThumb in der Version $1 oder höher.',
	'timedmedia-oggThumb-failed' => 'oggThumb konnte kein Miniaturbild erstellen.',
	'timedmedia-source-file' => 'Quelle ($1)',
	'timedmedia-source-file-desc' => 'Original $1, $2 x $3 ($4)',
	'timedmedia-derivative-220_200kbs.ogv' => 'Ogg (200p)',
	'timedmedia-derivative-desc-220_200kbs.ogv' => 'Ogg-Videodatei mit niedriger Datenübertragungsrate (200p)',
	'timedmedia-derivative-desc-360_400kbs.ogv' => 'Webstreamingfähige Ogg-Videodatei (360p)',
	'timedmedia-derivative-desc-480_600kbs.ogv' => 'Webstreamingfähige Ogg-Videodatei (480p)',
	'timedmedia-derivative-desc-720_VBR.ogv' => 'Qualitativ hochwertige Ogg-Videodatei (720p)',
	'timedmedia-derivative-desc-480_600kbs.webm' => 'Webstreamingfähige WebM-Videodatei (480p)',
	'timedmedia-derivative-desc720_VBR.webm' => 'Qualitativ hochwertige WebM-Videodatei (720p)',
	'timedmedia-subtitle-language' => '$1 ($2) Untertitel',
);

/** Zazaki (Zazaki)
 * @author Aspar
 * @author Xoser
 */
$messages['diq'] = array(
	'timedmedia-desc' => 'Qe dosyayanê Ogg Theora u Vorbisî pê JavaScriptî qulp',
	'timedmedia-ogg-short-audio' => 'Ogg $1 dosyaya vengi, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 dosyaya filmi, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 dosyaya medyayi, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 dosyaya medyayi,  mudde $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 dosyaya filmi, mudde $2, $4×$5 piksel, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg dosyaya filmi/vengi yo multiexed, $1, mudde $2, $4×$5 piksel, $3 bıumumi',
	'timedmedia-ogg-long-general' => 'Ogg dosyaya medyayi, mudde $2, $3',
	'timedmedia-ogg-long-error' => 'dosyaya oggi yo nemeqbul: $1',
	'timedmedia-more' => 'hema....',
	'timedmedia-dismiss' => 'bıqefeln',
	'timedmedia-download' => 'dosya biyar war',
	'timedmedia-desc-link' => 'derheqê dosyayi de',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'timedmedia-desc' => 'Wóźeński program za awdio, wideo a timedText, z formatoweju pódpěru za WebM, Ogg Theora, Vorbis, srt',
	'timedmedia-ogg-short-audio' => 'Ogg $1 awdiodataja, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 wideodataja, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 medijowa dataja, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 awdiodataja, dłujkosć $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 wideodataja, dłujkosć $2, $4×$5 pikselow, $3',
	'timedmedia-ogg-long-multiplexed' => 'ogg multipleksowa awdio-/wideodataja, $1, dłujkosć $2, $4×$5 pikselow, $3 dogromady',
	'timedmedia-ogg-long-general' => 'Ogg medijowa dataja, dłujkosć $2, $3',
	'timedmedia-ogg-long-error' => 'Njepłaśiwa Ogg-dataja: $1',
	'timedmedia-webm-short-video' => 'WebM $1 wideodataja, $2',
	'timedmedia-webm-long-video' => 'WebM dataja awdio/wideo, $1, dłujkosć $2, $4 x $5, $3 dogromady',
	'timedmedia-no-player-js' => 'Twój wobglědowak jo bóžko pak JavaScript znjemóžnił abo njama njepódpěrany wótegrawak.<br />
Móžoš  <a href="$1">klip ześěgnuś</a> abo <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">wótgrawak ześěgnuś</a>, aby klip w swójom wobglědowaku wótegrał.',
	'timedmedia-more' => 'Wěcej...',
	'timedmedia-dismiss' => 'Zacyniś',
	'timedmedia-download' => 'Dataju ześěgnuś',
	'timedmedia-play-media' => 'Medijowu dataju wótegraś',
	'timedmedia-desc-link' => 'Wó toś tej dataji',
	'timedmedia-oggThumb-version' => 'OggHandler trjeba wersiju $1 oggThumb abo nowšu.',
	'timedmedia-oggThumb-failed' => 'oggThumb njejo mógł wobrazk napóraś.',
	'timedmedia-source-file' => 'Žrědło ($1)',
	'timedmedia-source-file-desc' => 'Original $1, $2 x $3 ($4)',
	'timedmedia-derivative-220_200kbs.ogv' => 'Ogg (200p)',
	'timedmedia-derivative-desc-220_200kbs.ogv' => 'Niska šyrokosć pasma Ogg-widea (200p)',
	'timedmedia-subtitle-language' => '$1 ($2) pódtitele',
);

/** Greek (Ελληνικά)
 * @author Consta
 * @author Dead3y3
 * @author Omnipaedista
 * @author ZaDiak
 */
$messages['el'] = array(
	'timedmedia-desc' => 'Χειριστής για αρχεία Ogg Theora και Vorbis, με αναπαραγωγέα JavaScript',
	'timedmedia-ogg-short-audio' => 'Αρχείο ήχου Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Αρχείο βίντεο Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Αρχείο μέσων Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'Αρχείο ήχου Ogg $1, διάρκεια $2, $3',
	'timedmedia-ogg-long-video' => 'Αρχείο βίντεο Ogg $1, διάρκεια $2, $4×$5 pixels, $3',
	'timedmedia-ogg-long-multiplexed' => 'Αρχείο πολυπλεκτικού ήχου/βίντεο Ogg, $1, διάρκεια $2, $4×$5 pixels, $3 ολικά',
	'timedmedia-ogg-long-general' => 'Αρχείο μέσων Ogg, διάρκεια $2, $3',
	'timedmedia-ogg-long-error' => 'Άκυρο αρχείο ogg: $1',
	'timedmedia-more' => 'Περισσότερα...',
	'timedmedia-dismiss' => 'Κλείσιμο',
	'timedmedia-download' => 'Κατεβάστε το αρχείο',
	'timedmedia-desc-link' => 'Σχετικά με αυτό το αρχείο',
);

/** Esperanto (Esperanto)
 * @author Amikeco
 * @author ArnoLagrange
 * @author Yekrats
 */
$messages['eo'] = array(
	'timedmedia-desc' => 'Traktilo por dosieroj Ogg Theora kaj Vobis kun Ĵavaskripta legilo.',
	'timedmedia-ogg-short-audio' => 'Ogg $1 sondosiero, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 videodosiero, $2',
	'timedmedia-ogg-short-general' => 'Media Ogg-dosiero $1, $2',
	'timedmedia-ogg-long-audio' => 'Aŭda Ogg-dosiero $1, longeco $2, $3 entute',
	'timedmedia-ogg-long-video' => 'Video Ogg-dosiero $1, longeco $2, $4×$5 pikseloj, $3 entute',
	'timedmedia-ogg-long-multiplexed' => 'Kunigita aŭdio/video Ogg-dosiero, $1, longeco $2, $4×$5 pikseloj, $3 entute',
	'timedmedia-ogg-long-general' => 'Ogg-mediodosiero, longeco $2, $3',
	'timedmedia-ogg-long-error' => 'Malvalida Ogg-dosiero: $1',
	'timedmedia-more' => 'Pli...',
	'timedmedia-dismiss' => 'Fermi',
	'timedmedia-download' => 'Alŝuti dosieron',
	'timedmedia-desc-link' => 'Pri ĉi tiu dosiero',
);

/** Spanish (Español)
 * @author Aleator
 * @author Crazymadlover
 * @author Muro de Aguas
 * @author Remember the dot
 * @author Sanbec
 * @author Spacebirdy
 * @author Translationista
 */
$messages['es'] = array(
	'timedmedia-desc' => 'Herramienta de control de elementos multimedia sincronizados (vídeo, sonido y texto sincronizado) con transcodificación Ogg Theora y Vorbis',
	'timedmedia-ogg-short-audio' => 'Archivo de sonido Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Archivo de video Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Archivo Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'Archivo de sonido Ogg $1, tamaño $2, $3',
	'timedmedia-ogg-long-video' => 'Archivo de video Ogg $1, tamaño $2, $4×$5 píxeles, $3',
	'timedmedia-ogg-long-multiplexed' => 'Archivo Ogg de audio/video multiplexado, $1, tamaño $2, $4×$5 píxeles, $3 en todo',
	'timedmedia-ogg-long-general' => 'Archivo Ogg. tamaño $2, $3',
	'timedmedia-ogg-long-error' => 'Archivo Ogg no válido: $1',
	'timedmedia-no-player-js' => 'Lo sentimos, pero tu navegador tiene JavaScript inhabilitado o no tiene ningún reproductor compatible instalado.<br />
Puedes <a href="$1">descargar el clip</a> o <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">descargar un reproductor</a> para poder ver el vídeo en tu navegador.',
	'timedmedia-more' => 'Opciones...',
	'timedmedia-dismiss' => 'Cerrar',
	'timedmedia-download' => 'Descargar archivo',
	'timedmedia-desc-link' => 'Sobre este archivo',
	'timedmedia-oggThumb-version' => 'OggHandler requiere una versión oggThumb $1 o posterior.',
	'timedmedia-oggThumb-failed' => 'oggThumb no pudo crear la imagen miniatura.',
);

/** Estonian (Eesti)
 * @author Avjoska
 * @author Pikne
 * @author Silvar
 */
$messages['et'] = array(
	'timedmedia-desc' => 'Ogg Theora ja Vorbis failide töötleja JavaScript-esitajaga.',
	'timedmedia-ogg-long-error' => 'Vigane Ogg-fail: $1',
	'timedmedia-more' => 'Lisa...',
	'timedmedia-dismiss' => 'Sule',
	'timedmedia-download' => 'Laadi fail alla',
	'timedmedia-desc-link' => 'Info faili kohta',
);

/** Basque (Euskara)
 * @author An13sa
 * @author Joxemai
 * @author Theklan
 */
$messages['eu'] = array(
	'timedmedia-desc' => 'Ogg Theora eta Vorbis fitxategientzako edukiontzia, JavaScript playerrarekin',
	'timedmedia-ogg-short-audio' => 'Ogg $1 soinu fitxategia, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 bideo fitxategia, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 media fitxategia, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 soinu fitxategia, $2 iraupea, $3',
	'timedmedia-ogg-long-error' => 'ogg fitxategi okerra: $1',
	'timedmedia-more' => 'Gehiago...',
	'timedmedia-dismiss' => 'Itxi',
	'timedmedia-download' => 'Fitxategia jaitsi',
	'timedmedia-desc-link' => 'Fitxategi honen inguruan',
);

/** Persian (فارسی)
 * @author Huji
 */
$messages['fa'] = array(
	'timedmedia-desc' => 'به دست گیرندهٔ پرونده‌های صوتی، تصویری و متن‌های زمان‌بندی شده با پشتیبانی از Ogg Theora ،Vorbis و srt',
	'timedmedia-ogg-short-audio' => 'پرونده صوتی Ogg $1، $2',
	'timedmedia-ogg-short-video' => 'پرونده تصویری Ogg $1، $2',
	'timedmedia-ogg-short-general' => 'پرونده Ogg $1، $2',
	'timedmedia-ogg-long-audio' => 'پرونده صوتی Ogg $1، مدت $2، $3',
	'timedmedia-ogg-long-video' => 'پرونده تصویری Ogg $1، مدت $2 ، $4×$5 پیکسل، $3',
	'timedmedia-ogg-long-multiplexed' => 'پرونده صوتی/تصویری پیچیده Ogg، $1، مدت $2، $4×$5 پیکسل، $3 در مجموع',
	'timedmedia-ogg-long-general' => 'پرونده Ogg، مدت $2، $3',
	'timedmedia-ogg-long-error' => 'پرونده Ogg غیرمجاز: $1',
	'timedmedia-more' => 'بیشتر...',
	'timedmedia-dismiss' => 'بستن',
	'timedmedia-download' => 'بارگیری پرونده',
	'timedmedia-desc-link' => 'دربارهٔ این پرونده',
);

/** Finnish (Suomi)
 * @author Agony
 * @author Crt
 * @author Nike
 * @author Str4nd
 */
$messages['fi'] = array(
	'timedmedia-desc' => 'Käsittelijä Ogg Theora ja Vorbis -tiedostoille ja JavaScript-soitin.',
	'timedmedia-ogg-short-audio' => 'Ogg $1 -äänitiedosto, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 -videotiedosto, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 -mediatiedosto, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 -äänitiedosto, $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 -videotiedosto, $2, $4×$5, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg-tiedosto limitetty kuva ja ääni, $1, $2, $4×$5, $3',
	'timedmedia-ogg-long-general' => 'Ogg-tiedosto, $2, $3',
	'timedmedia-ogg-long-error' => 'Kelvoton Ogg-tiedosto: $1',
	'timedmedia-more' => 'Lisää…',
	'timedmedia-dismiss' => 'Sulje',
	'timedmedia-download' => 'Lataa',
	'timedmedia-desc-link' => 'Tiedoston tiedot',
);

/** Faroese (Føroyskt)
 * @author Spacebirdy
 */
$messages['fo'] = array(
	'timedmedia-more' => 'Meira...',
);

/** French (Français)
 * @author Crochet.david
 * @author Grondin
 * @author IAlex
 * @author Jean-Frédéric
 * @author Peter17
 * @author Seb35
 * @author Sherbrooke
 * @author Urhixidur
 * @author Verdy p
 */
$messages['fr'] = array(
	'timedmedia-desc' => 'Support pour les vidéo, audio et texte synchronisé avec support des formats WebM, Ogg Theora, Vorbis et srt',
	'timedmedia-ogg-short-audio' => 'Fichier son Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Fichier vidéo Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Fichier média Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'Fichier son Ogg $1, durée $2, $3',
	'timedmedia-ogg-long-video' => 'Fichier vidéo Ogg $1, durée $2, $4×$5 pixels, $3',
	'timedmedia-ogg-long-multiplexed' => 'Fichier multiplexé audio/vidéo Ogg, $1, durée $2, $4×$5 pixels, $3',
	'timedmedia-ogg-long-general' => 'Fichier média Ogg, durée $2, $3',
	'timedmedia-ogg-long-error' => 'Fichier Ogg invalide : $1',
	'timedmedia-webm-short-video' => 'Fichier vidéo WebM $1, $2',
	'timedmedia-webm-long-video' => "Fichier audio/vidéo WebM, $1, longueur $2, $4 x $5 pixels, $3 l'ensemble",
	'timedmedia-no-player-js' => 'Désolé, votre navigateur doit soit avoir JavaScript désactivé ou n\'a pas un lecteur pris en charge.<br />
Vous pouvez <a href="$1">télécharger le clip</a> ou <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">télécharger un lecteur</a> pour lire le clip dans votre navigateur.',
	'timedmedia-more' => 'Plus…',
	'timedmedia-dismiss' => 'Fermer',
	'timedmedia-download' => 'Télécharger le fichier',
	'timedmedia-desc-link' => 'À propos de ce fichier',
	'timedmedia-oggThumb-version' => 'OggHandler nécessite oggThumb, version $1 ou supérieure.',
	'timedmedia-oggThumb-failed' => 'oggThumb n’a pas réussi à créer la miniature.',
	'timedmedia-source-file' => 'Source $1',
	'timedmedia-source-file-desc' => 'Initial $1, $2 × $3 ($4)',
	'timedmedia-derivative-220_200kbs.ogv' => 'Ogg 200p',
	'timedmedia-derivative-desc-220_200kbs.ogv' => 'Vidéo Ogg bas débit (200P)',
	'timedmedia-derivative-desc-360_400kbs.ogv' => 'Vidéo Ogg lisible en continu sur le Web (360p)',
	'timedmedia-derivative-desc-480_600kbs.ogv' => 'Vidéo Ogg lisible en continu sur le web (480p)',
	'timedmedia-derivative-desc-720_VBR.ogv' => 'Vidéo Ogg téléchargeable de grande qualité (720p)',
	'timedmedia-derivative-desc-480_600kbs.webm' => 'WebM lisible en continu depuis le web (480p)',
	'timedmedia-derivative-desc720_VBR.webm' => 'Vidéo WebM téléchargeable de grande qualité (720p)',
	'timedmedia-subtitle-language' => 'soustitres en $1 ($2)',
);

/** Franco-Provençal (Arpetan)
 * @author ChrisPtDe
 */
$messages['frp'] = array(
	'timedmedia-desc' => 'Assistance por los fichiérs multimèdia que dèfilont (vidèô, ôdiô, tèxto sincronisâ) avouéc transcodâjo en Ogg Theora / Vorbis.',
	'timedmedia-ogg-short-audio' => 'Fichiér son Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Fichiér vidèô Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Fichiér multimèdia Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'Fichiér son Ogg $1, temps $2, $3',
	'timedmedia-ogg-long-video' => 'Fichiér vidèô Ogg $1, temps $2, $4×$5 pixèls, $3',
	'timedmedia-ogg-long-multiplexed' => 'Fichiér multiplèxo ôdiô / vidèô Ogg, $1, temps $2, $4×$5 pixèls, $3',
	'timedmedia-ogg-long-general' => 'Fichiér multimèdia Ogg, temps $2, $3',
	'timedmedia-ogg-long-error' => 'Fichiér Ogg envalido : $1',
	'timedmedia-more' => 'De ples...',
	'timedmedia-dismiss' => 'Cllôre',
	'timedmedia-download' => 'Tèlèchargiér lo fichiér',
	'timedmedia-desc-link' => 'A propôs de ceti fichiér',
	'timedmedia-oggThumb-version' => 'OggHandler at fôta d’oggThumb, vèrsion $1 ou ben ples novèla.',
	'timedmedia-oggThumb-failed' => 'oggThumb at pas reussi a fâre la figura.',
);

/** Friulian (Furlan)
 * @author Klenje
 */
$messages['fur'] = array(
	'timedmedia-desc' => 'Gjestôr pai files Ogg Theora e Vorbis, cuntun riprodutôr JavaScript',
	'timedmedia-ogg-short-audio' => 'File audio Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'File video Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'File multimediâl Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'File audio Ogg $1, durade $2, $3',
	'timedmedia-ogg-long-video' => 'File video Ogg $1, durade $2, dimensions $4×$5 pixels, $3',
	'timedmedia-ogg-long-multiplexed' => 'File audio/video multiplexed Ogg $1, lungjece $2, dimensions $4×$5 pixels, in dut $3',
	'timedmedia-ogg-long-general' => 'File multimediâl Ogg, durade $2, $3',
	'timedmedia-ogg-long-error' => 'File ogg no valit: $1',
	'timedmedia-more' => 'Altri...',
	'timedmedia-dismiss' => 'Siere',
	'timedmedia-download' => 'Discjame il file',
	'timedmedia-desc-link' => 'Informazions su chest file',
);

/** Irish (Gaeilge)
 * @author Spacebirdy
 */
$messages['ga'] = array(
	'timedmedia-dismiss' => 'Dún',
);

/** Galician (Galego)
 * @author Toliño
 * @author Xosé
 */
$messages['gl'] = array(
	'timedmedia-desc' => 'Manipulador de son, vídeo e texto sincronizado, con soporte para os formatos WebM, Ogg Theora, Vorbis e srt',
	'timedmedia-ogg-short-audio' => 'Ficheiro de son Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Ficheiro de vídeo Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Ficheiro multimedia Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'Ficheiro de son Ogg $1, duración $2, $3',
	'timedmedia-ogg-long-video' => 'Ficheiro de vídeo Ogg $1, duración $2, $4×$5 píxeles, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ficheiro de son/vídeo Ogg multiplex, $1, duración $2, $4×$5 píxeles, $3 total',
	'timedmedia-ogg-long-general' => 'Ficheiro multimedia Ogg, duración $2, $3',
	'timedmedia-ogg-long-error' => 'Ficheiro Ogg non válido: $1',
	'timedmedia-webm-short-video' => 'Ficheiro de vídeo WebM $1, $2',
	'timedmedia-webm-long-video' => 'Ficheiro WebM de son/vídeo, $1, duración $2, $4×$5 píxeles, $3 total',
	'timedmedia-no-player-js' => 'O seu navegador ten o JavaScript desactivado ou non conta con ningún reprodutor dos soportados.<br />
Pode <a href="$1">descargar o vídeo</a> ou <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">un reprodutor</a> para reproducir o vídeo no seu navegador.',
	'timedmedia-more' => 'Máis...',
	'timedmedia-dismiss' => 'Fechar',
	'timedmedia-download' => 'Descargar o ficheiro',
	'timedmedia-desc-link' => 'Acerca deste ficheiro',
	'timedmedia-oggThumb-version' => 'O OggHandler necesita a versión $1 ou unha posterior do oggThumb.',
	'timedmedia-oggThumb-failed' => 'Houbo un erro por parte do oggThumb ao crear a miniatura.',
	'timedmedia-source-file' => 'Fonte',
	'timedmedia-source-file-desc' => 'Fonte $1, $2 × $3 ($4)',
	'timedmedia-derivative-220_200kbs.ogv' => 'Ogg 200P',
	'timedmedia-derivative-desc-220_200kbs.ogv' => 'Vídeo Ogg de baixo ancho de banda (200P)',
	'timedmedia-derivative-desc-360_400kbs.ogv' => 'Vídeo Ogg para a web (360P)',
	'timedmedia-derivative-desc-480_600kbs.ogv' => 'Vídeo Ogg para a web (480P)',
	'timedmedia-derivative-desc-720_VBR.ogv' => 'Vídeo Ogg de alta calidade que se pode descargar (720P)',
	'timedmedia-derivative-desc-480_600kbs.webm' => 'WebM para a web (480P)',
	'timedmedia-derivative-desc720_VBR.webm' => 'WebM de alta calidade que se pode descargar (720P)',
	'timedmedia-subtitle-language' => 'Subtítulos en $1 ($2)',
);

/** Ancient Greek (Ἀρχαία ἑλληνικὴ)
 * @author Crazymadlover
 * @author Flyax
 * @author Omnipaedista
 */
$messages['grc'] = array(
	'timedmedia-ogg-long-error' => 'Ἄκυρα ἀρχεῖα ogg: $1',
	'timedmedia-more' => 'πλέον...',
	'timedmedia-dismiss' => 'Κλῄειν',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 * @author Melancholie
 */
$messages['gsw'] = array(
	'timedmedia-desc' => 'Stellt e Styyrigsprogramm fir zytgstyyrti Medie (Video, Audio, timedText) mit dr Codeumwandlig no Ogg Theora/Vorbis z Verfiegig',
	'timedmedia-ogg-short-audio' => 'Ogg-$1-Audiodatei, $2',
	'timedmedia-ogg-short-video' => 'Ogg-$1-Videodatei, $2',
	'timedmedia-ogg-short-general' => 'Ogg-$1-Mediadatei, $2',
	'timedmedia-ogg-long-audio' => 'Ogg-$1-Audiodatei, Längi: $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg-$1-Videodatei, Längi: $2, $4×$5 Pixel, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg-Audio-/Video-Datei, $1, Längi: $2, $4×$5 Pixel, $3',
	'timedmedia-ogg-long-general' => 'Ogg-Mediadatei, Längi: $2, $3',
	'timedmedia-ogg-long-error' => 'Uugiltigi Ogg-Datei: $1',
	'timedmedia-no-player-js' => 'Excusez, aber Dyy Browser het entwäder JavaScript deaktiviert oder kei unterstitzti Abspilsoftware.<br />
Du chasch <a href="$1">dr Clip abelade</a> oder <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">e Abspielsoftware abelade</a> go dr Clip im Browser abspile.',
	'timedmedia-more' => 'Meh …',
	'timedmedia-dismiss' => 'Zuemache',
	'timedmedia-download' => 'Datei spychere',
	'timedmedia-desc-link' => 'Iber die Datei',
	'timedmedia-oggThumb-version' => 'OggHandler brucht oggThumb in dr Version $1 oder hecher.',
	'timedmedia-oggThumb-failed' => 'oggThumb het kei Miniaturbild chenne aalege.',
);

/** Manx (Gaelg)
 * @author MacTire02
 */
$messages['gv'] = array(
	'timedmedia-desc-link' => 'Mychione y choadan shoh',
);

/** Hebrew (עברית)
 * @author Amire80
 * @author Rotem Liss
 * @author Rotemliss
 * @author YaronSh
 */
$messages['he'] = array(
	'timedmedia-desc' => 'מציג מדיה למדיה מתוזמנת – וידאו, שמע, טקסט מתוזמן – עם קידוד מחדש ל־Ogg Theora או Vorbis',
	'timedmedia-ogg-short-audio' => 'קובץ שמע $1 של Ogg, $2',
	'timedmedia-ogg-short-video' => 'קובץ וידאו $1 של Ogg, $2',
	'timedmedia-ogg-short-general' => 'קובץ מדיה $1 של Ogg, $2',
	'timedmedia-ogg-long-audio' => 'קובץ שמע $1 של Ogg, באורך $2, $3',
	'timedmedia-ogg-long-video' => 'קובץ וידאו $1 של Ogg, באורך $2, $4×$5 פיקסלים, $3',
	'timedmedia-ogg-long-multiplexed' => 'קובץ Ogg מרובב של שמע ווידאו, $1, באורך $2, $4×$5 פיקסלים, $3 בסך הכול',
	'timedmedia-ogg-long-general' => 'קובץ מדיה של Ogg, באורך $2, $3',
	'timedmedia-ogg-long-error' => 'קובץ ogg בלתי תקין: $1',
	'timedmedia-no-player-js' => 'סליחה, בדפדפן שלכם לא מופעלת תמיכה ב־JavaScript או שאין לכם נגן נתמך.<br />
אתם יכולים <a href="$1">להוריד למחשב את הסרטון</a> או <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">להוריד למחשב שלכם נגן</a> שינגן את הסרטון בדפדפן שלכם.',
	'timedmedia-more' => 'עוד…',
	'timedmedia-dismiss' => 'סגירה',
	'timedmedia-download' => 'הורדת הקובץ',
	'timedmedia-desc-link' => 'אודות הקובץ',
	'timedmedia-oggThumb-version' => 'OggHandler דורש oggThumb מגרסה $1 או גרסה חדשה יותר.',
	'timedmedia-oggThumb-failed' => 'oggThumb לא הצליח ליצור תמונה מוקטנת.',
);

/** Hindi (हिन्दी)
 * @author Kaustubh
 * @author Shyam
 */
$messages['hi'] = array(
	'timedmedia-desc' => 'ऑग थियोरा और वॉर्बिस फ़ाईल्सके लिये चालक, जावास्क्रीप्ट प्लेयर के साथ',
	'timedmedia-ogg-short-audio' => 'ऑग $1 ध्वनी फ़ाईल, $2',
	'timedmedia-ogg-short-video' => 'ऑग $1 चलतचित्र फ़ाईल, $2',
	'timedmedia-ogg-short-general' => 'ऑग $1 मीडिया फ़ाईल, $2',
	'timedmedia-ogg-long-audio' => 'ऑग $1 ध्वनी फ़ाईल, लंबाई $2, $3',
	'timedmedia-ogg-long-video' => 'ऑग $1 चलतचित्र फ़ाईल, लंबाई $2, $4×$5 पीक्सेल्स, $3',
	'timedmedia-ogg-long-multiplexed' => 'ऑग ध्वनी/चित्र फ़ाईल, $1, लंबाई $2, $4×$5 पिक्सेल्स, $3 कुल',
	'timedmedia-ogg-long-general' => 'ऑग मीडिया फ़ाईल, लंबाई $2, $3',
	'timedmedia-ogg-long-error' => 'गलत ऑग फ़ाईल: $1',
	'timedmedia-more' => 'और...',
	'timedmedia-dismiss' => 'बंद करें',
	'timedmedia-download' => 'फ़ाईल डाउनलोड करें',
	'timedmedia-desc-link' => 'इस फ़ाईलके बारे में',
);

/** Croatian (Hrvatski)
 * @author CERminator
 * @author Dalibor Bosits
 * @author Ex13
 * @author SpeedyGonsales
 */
$messages['hr'] = array(
	'timedmedia-desc' => 'Poslužitelj za Ogg Theora i Vorbis datoteke, s JavaScript preglednikom',
	'timedmedia-ogg-short-audio' => 'Ogg $1 zvučna datoteka, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 video datoteka, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 medijska datoteka, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 zvučna datoteka, duljine $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 video datoteka, duljine $2, $4x$5 piksela, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg multipleksirana zvučna/video datoteka, $1, duljine $2, $4×$5 piksela, $3 ukupno',
	'timedmedia-ogg-long-general' => 'Ogg medijska datoteka, duljine $2, $3',
	'timedmedia-ogg-long-error' => 'nevaljana ogg datoteka: $1',
	'timedmedia-more' => 'Više...',
	'timedmedia-dismiss' => 'Zatvori',
	'timedmedia-download' => 'Snimi datoteku',
	'timedmedia-desc-link' => 'O ovoj datoteci',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Dundak
 * @author Michawiki
 * @author Reedy
 */
$messages['hsb'] = array(
	'timedmedia-desc' => 'Wodźenski program za awdio, widejo a timedText z podpěru za formaty WebM, Ogg Theora, Vorbis, srt',
	'timedmedia-ogg-short-audio' => 'Awdiodataja Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Widejodataja Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Ogg medijowa dataja $1, $2',
	'timedmedia-ogg-long-audio' => 'Ogg-awdiodataja $1, dołhosć: $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg-widejodataja $1, dołhosć: $2, $4×$5 pikselow, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg multipleksna awdio-/widejodataja, $1, dołhosć: $2, $4×$5 pikselow, $3',
	'timedmedia-ogg-long-general' => 'Ogg medijowa dataja, dołhosć: $2, $3',
	'timedmedia-ogg-long-error' => 'Njepłaćiwa Ogg-dataja: $1',
	'timedmedia-webm-short-video' => 'Widejodataja WebM $1, $2',
	'timedmedia-webm-long-video' => 'Awdio-/widejodataja WebM, $1, dołhosć $2, $4 x $5 pikselow, $3 dohromady',
	'timedmedia-no-player-js' => 'Twój wobhladowak je pak JavaScript znjemóžnił pak nima podpěrowany wothrawak.<br />
Móžeš <a href="$1">klip sćahnyć</a> abo <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">wothrawak sćahnyć</a>, zo by klip w swojim wobhladowaku wothrawał.',
	'timedmedia-more' => 'Wjace ...',
	'timedmedia-dismiss' => 'Začinić',
	'timedmedia-download' => 'Dataju sćahnyć',
	'timedmedia-play-media' => 'Medijowu dataju wothrać',
	'timedmedia-desc-link' => 'Wo tutej dataji',
	'timedmedia-oggThumb-version' => 'OggHandler trjeba wersiju $1 oggThumb abo nowšu.',
	'timedmedia-oggThumb-failed' => 'oggThumb njemóžeše wobrazk wutworić.',
	'timedmedia-source-file' => 'Žórło ($1)',
	'timedmedia-source-file-desc' => 'Originalny $1, $2 x $3 ($4)',
	'timedmedia-derivative-220_200kbs.ogv' => 'Ogg (200p)',
	'timedmedia-derivative-desc-220_200kbs.ogv' => 'Ogg-widejo z niskej šěrokosću pasma (200p)',
	'timedmedia-derivative-desc-360_400kbs.ogv' => 'Ogg-widejo (360p), kotrež da so přez Web přenjesć',
	'timedmedia-derivative-desc-480_600kbs.ogv' => 'Ogg-widejo (480p), kotrež da so přez Web přenjesć',
	'timedmedia-derivative-desc-720_VBR.ogv' => 'Sćahujomne Ogg-widejo wysokeje kwality (720p)',
	'timedmedia-derivative-desc-480_600kbs.webm' => 'WebM-widejo (480p), kotrež da so přez Web přenjesć',
	'timedmedia-derivative-desc720_VBR.webm' => 'Sćahujomna WebM-dataja wysokeje kwality (720p)',
	'timedmedia-subtitle-language' => '$1 ($2) podtitule',
);

/** Hungarian (Magyar)
 * @author Dani
 * @author Glanthor Reviol
 * @author Tgr
 */
$messages['hu'] = array(
	'timedmedia-desc' => 'JavaScript nyelven írt lejátszó Ogg Theora és Vorbis fájlokhoz',
	'timedmedia-ogg-short-audio' => 'Ogg $1 hangfájl, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 videofájl, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 médiafájl, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 hangfájl, hossza: $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 videófájl, hossza $2, $4×$5 képpont, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg egyesített audió- és videófájl, $1, hossz: $2, $4×$5 képpont, $3 összesen',
	'timedmedia-ogg-long-general' => 'Ogg médiafájl, hossza: $2, $3',
	'timedmedia-ogg-long-error' => 'Érvénytelen ogg fájl: $1',
	'timedmedia-no-player-js' => 'Sajnáljuk, a böngésződben vagy le van tiltva a JavaScript, vagy nincs egyetlen támogatott lejátszója sem.<br />
<a href="$1">Letöltheted a klipet</a>, vagy <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">letölthetsz egy lejátszót</a> a böngészőben való megtekintéshez.',
	'timedmedia-more' => 'Tovább...',
	'timedmedia-dismiss' => 'Bezárás',
	'timedmedia-download' => 'Fájl letöltése',
	'timedmedia-desc-link' => 'Fájlinformációk',
	'timedmedia-oggThumb-version' => 'Az OggHandlerhez $1 vagy későbbi verziójú oggThumb szükséges.',
	'timedmedia-oggThumb-failed' => 'Az oggThumb nem tudta elkészíteni a bélyegképet.',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'timedmedia-desc' => 'Gestor pro audio, video e texto synchronisate, con supporto del formatos WebM, Ogg Theora, Vorbis, srt',
	'timedmedia-ogg-short-audio' => 'File audio Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'File video Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'File media Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'File audio Ogg $1, duration $2, $3',
	'timedmedia-ogg-long-video' => 'File video Ogg $1, duration $2, $4×$5 pixel, $3',
	'timedmedia-ogg-long-multiplexed' => 'File multiplexate audio/video Ogg, $1, duration $2, $4×$5 pixel, $3 in total',
	'timedmedia-ogg-long-general' => 'File media Ogg, duration $2, $3',
	'timedmedia-ogg-long-error' => 'File Ogg invalide: $1',
	'timedmedia-webm-short-video' => 'File video WebM $1, $2',
	'timedmedia-webm-long-video' => 'File audio/video WebM, $1, longitude $2, $4 × $5 pixels, $3 in total',
	'timedmedia-no-player-js' => 'Pardono, tu systema o ha JavaScript disactivate o non ha un reproductor supportate.<br />
Tu pote <a href="$1">discargar le clip</a> o <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">discargar un reproductor</a> pro reproducer le clip in tu navigator.',
	'timedmedia-more' => 'Plus…',
	'timedmedia-dismiss' => 'Clauder',
	'timedmedia-download' => 'Discargar file',
	'timedmedia-play-media' => 'Reproducer multimedia',
	'timedmedia-desc-link' => 'A proposito de iste file',
	'timedmedia-oggThumb-version' => 'OggHandler require oggThumb version $1 o plus recente.',
	'timedmedia-oggThumb-failed' => 'oggThumb ha fallite de crear le miniatura.',
	'timedmedia-source-file' => 'original $1',
	'timedmedia-source-file-desc' => 'Original $1, $2 × $3 ($4)',
	'timedmedia-derivative-220_200kbs.ogv' => 'Ogg 200P',
	'timedmedia-derivative-desc-220_200kbs.ogv' => 'Video Ogg a basse largor de banda (200P)',
	'timedmedia-derivative-desc-360_400kbs.ogv' => 'Video Ogg fluibile per web (360P)',
	'timedmedia-derivative-desc-480_600kbs.ogv' => 'Video Ogg fluibile per web (480P)',
	'timedmedia-derivative-desc-720_VBR.ogv' => 'Video Ogg discargabile de alte qualitate (720P)',
	'timedmedia-derivative-desc-480_600kbs.webm' => 'WebM fluibile per web (480P)',
	'timedmedia-derivative-desc720_VBR.webm' => 'WebM discargabile de alte qualitate (720P)',
	'timedmedia-subtitle-language' => '$1 ($2) subtitulos',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 * @author Farras
 * @author Irwangatot
 * @author IvanLanin
 * @author Rex
 */
$messages['id'] = array(
	'timedmedia-desc' => 'Pemroses media berwaktu (video, audio, timedText) dengan transkode ke Ogg Theora/Vorbis',
	'timedmedia-ogg-short-audio' => 'Berkas suara $1 ogg, $2',
	'timedmedia-ogg-short-video' => 'Berkas video $1 ogg, $2',
	'timedmedia-ogg-short-general' => 'Berkas media $1 ogg, $2',
	'timedmedia-ogg-long-audio' => 'Berkas suara $1 ogg, panjang $2, $3',
	'timedmedia-ogg-long-video' => 'Berkas video $1 ogg, panjang $2, $4×$5 piksel, $3',
	'timedmedia-ogg-long-multiplexed' => 'Berkas audio/video multiplexed ogg, $1, panjang $2, $4×$5 piksel, $3 keseluruhan',
	'timedmedia-ogg-long-general' => 'Berkas media ogg, panjang $2, $3',
	'timedmedia-ogg-long-error' => 'Berkas ogg tak valid: $1',
	'timedmedia-no-player-js' => 'Maaf, peramban Anda memiliki JavaScript yang dinonaktifkan atau tidak memiliki pemutar media apapun.<br />
Anda dapat <a href="$1">mengunduh klip</a> atau <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">mengunduh pemutar</a> untuk memutar klip di peramban Anda.',
	'timedmedia-more' => 'Lainnya...',
	'timedmedia-dismiss' => 'Tutup',
	'timedmedia-download' => 'Unduh berkas',
	'timedmedia-desc-link' => 'Mengenai berkas ini',
	'timedmedia-oggThumb-version' => 'OggHandler membutuhkan oggThumb versi $1 atau terbaru.',
	'timedmedia-oggThumb-failed' => 'oggThumb gagal membuat miniatur gambar.',
);

/** Ido (Ido)
 * @author Malafaya
 */
$messages['io'] = array(
	'timedmedia-ogg-long-error' => 'Ne-valida Ogg-arkivo: $1',
	'timedmedia-more' => 'Plus…',
	'timedmedia-dismiss' => 'Klozar',
	'timedmedia-desc-link' => 'Pri ca arkivo',
);

/** Icelandic (Íslenska)
 * @author S.Örvarr.S
 * @author Spacebirdy
 */
$messages['is'] = array(
	'timedmedia-more' => 'Meira...',
	'timedmedia-dismiss' => 'Loka',
	'timedmedia-download' => 'Sækja skrá',
);

/** Italian (Italiano)
 * @author .anaconda
 * @author BrokenArrow
 * @author Darth Kule
 */
$messages['it'] = array(
	'timedmedia-desc' => 'Gestore per i file Ogg Theora e Vorbis, con programma di riproduzione in JavaScript',
	'timedmedia-ogg-short-audio' => 'File audio Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'File video Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'File multimediale Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'File audio Ogg $1, durata $2, $3',
	'timedmedia-ogg-long-video' => 'File video Ogg $1, durata $2, dimensioni $4×$5 pixel, $3',
	'timedmedia-ogg-long-multiplexed' => 'File audio/video multiplexed Ogg $1, durata $2, dimensioni $4×$5 pixel, complessivamente $3',
	'timedmedia-ogg-long-general' => 'File multimediale Ogg, durata $2, $3',
	'timedmedia-ogg-long-error' => 'File ogg non valido: $1',
	'timedmedia-more' => 'Altro...',
	'timedmedia-dismiss' => 'Chiudi',
	'timedmedia-download' => 'Scarica il file',
	'timedmedia-desc-link' => 'Informazioni su questo file',
);

/** Japanese (日本語)
 * @author Aotake
 * @author Fryed-peach
 * @author JtFuruhata
 * @author Kahusi
 * @author 青子守歌
 */
$messages['ja'] = array(
	'timedmedia-desc' => 'Theora および Vorbis 形式の Ogg ファイルハンドラーと JavaScript プレイヤー',
	'timedmedia-ogg-short-audio' => 'Ogg $1 音声ファイル、$2',
	'timedmedia-ogg-short-video' => 'Ogg $1 動画ファイル、$2',
	'timedmedia-ogg-short-general' => 'Ogg $1 メディアファイル、$2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 音声ファイル、長さ $2、$3',
	'timedmedia-ogg-long-video' => 'Ogg $1 動画ファイル、長さ $2、$4×$5px、$3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg 多重音声/動画ファイル、$1、長さ $2、$4×$5 ピクセル、$3',
	'timedmedia-ogg-long-general' => 'Ogg メディアファイル、長さ $2、$3',
	'timedmedia-ogg-long-error' => '無効な Ogg ファイル: $1',
	'timedmedia-no-player-js' => '申し訳ありません。あなたのブラウザはJavaScriptが有効でないか、プレイヤーをサポートしていません。<br />
再生するには、<a href="$1">クリップをダウンロード</a>するか、<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">プレイヤーをダウンロード</a>する必要があります。',
	'timedmedia-more' => 'その他……',
	'timedmedia-dismiss' => '閉じる',
	'timedmedia-download' => 'ファイルをダウンロード',
	'timedmedia-desc-link' => 'ファイルの詳細',
	'timedmedia-oggThumb-version' => 'OggHandler は oggThumb バージョン$1またはそれ以降が必要です。',
	'timedmedia-oggThumb-failed' => 'oggThumb によるサムネイル作成に失敗しました。',
);

/** Jutish (Jysk)
 * @author Huslåke
 */
$messages['jut'] = array(
	'timedmedia-desc' => 'Håndlær før Ogg Theora og Vorbis filer, ve JavaScript spæler',
	'timedmedia-ogg-short-audio' => 'Ogg $1 sond file, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 video file, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 media file, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 sond file, duråsje $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 video file, duråsje $2, $4×$5 piksel, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg multipleksen audio/video file, $1, duråsje $2, $4×$5 piksler, $3 åverål',
	'timedmedia-ogg-long-general' => 'Ogg $1 media file, duråsje $2, $3',
	'timedmedia-ogg-long-error' => 'Ugyldegt ogg file: $2',
	'timedmedia-more' => 'Mære...',
	'timedmedia-dismiss' => 'Slut',
	'timedmedia-download' => 'Nærlæĝ billet',
	'timedmedia-desc-link' => 'Åver dette file',
);

/** Javanese (Basa Jawa)
 * @author Meursault2004
 * @author Pras
 */
$messages['jv'] = array(
	'timedmedia-desc' => 'Sing ngurusi berkas Ogg Theora lan Vorbis mawa pamain JavaScript',
	'timedmedia-ogg-short-audio' => 'Berkas swara $1 ogg, $2',
	'timedmedia-ogg-short-video' => 'Berkas vidéo $1 ogg, $2',
	'timedmedia-ogg-short-general' => 'Berkas média $1 ogg, $2',
	'timedmedia-ogg-long-audio' => 'Berkas swara $1 ogg, dawané $2, $3',
	'timedmedia-ogg-long-video' => 'Berkas vidéo $1 ogg, dawané $2, $4×$5 piksel, $3',
	'timedmedia-ogg-long-multiplexed' => 'Berkas audio/vidéo multiplexed ogg, $1, dawané $2, $4×$5 piksel, $3 gunggungé',
	'timedmedia-ogg-long-general' => 'Berkas média ogg, dawané $2, $3',
	'timedmedia-ogg-long-error' => 'Berkas ogg ora absah: $1',
	'timedmedia-more' => 'Luwih akèh...',
	'timedmedia-dismiss' => 'Tutup',
	'timedmedia-download' => 'Undhuh berkas',
	'timedmedia-desc-link' => 'Prekara berkas iki',
);

/** Georgian (ქართული)
 * @author BRUTE
 * @author Malafaya
 * @author გიორგიმელა
 */
$messages['ka'] = array(
	'timedmedia-ogg-short-video' => 'Ogg $1 ვიდეო ფაილი, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 მედია ფაილი, $2',
	'timedmedia-more' => 'მეტი...',
	'timedmedia-dismiss' => 'დახურვა',
	'timedmedia-download' => 'ფაილის ჩამოტვირთვა',
	'timedmedia-desc-link' => 'ამ ფაილის შესახებ',
);

/** Kazakh (Arabic script) (‫قازاقشا (تٴوتە)‬) */
$messages['kk-arab'] = array(
	'timedmedia-ogg-short-audio' => 'Ogg $1 دىبىس فايلى, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 بەينە فايلى, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 تاسپا فايلى, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 دىبىس فايلى, ۇزاقتىعى $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 بەينە فايلى, ۇزاقتىعى $2, $4 × $5 پىيكسەل, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg قۇرامدى دىبىس/بەينە فايلى, $1, ۇزاقتىعى $2, $4 × $5 پىيكسەل, $3 نە بارلىعى',
	'timedmedia-ogg-long-general' => 'Ogg تاسپا فايلى, ۇزاقتىعى $2, $3',
	'timedmedia-ogg-long-error' => 'جارامسىز ogg فايلى: $1',
	'timedmedia-more' => 'كوبىرەك...',
	'timedmedia-dismiss' => 'جابۋ',
	'timedmedia-download' => 'فايلدى جۇكتەۋ',
	'timedmedia-desc-link' => 'بۇل فايل تۋرالى',
);

/** Kazakh (Cyrillic) (Қазақша (Cyrillic)) */
$messages['kk-cyrl'] = array(
	'timedmedia-ogg-short-audio' => 'Ogg $1 дыбыс файлы, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 бейне файлы, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 таспа файлы, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 дыбыс файлы, ұзақтығы $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 бейне файлы, ұзақтығы $2, $4 × $5 пиксел, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg құрамды дыбыс/бейне файлы, $1, ұзақтығы $2, $4 × $5 пиксел, $3 не барлығы',
	'timedmedia-ogg-long-general' => 'Ogg таспа файлы, ұзақтығы $2, $3',
	'timedmedia-ogg-long-error' => 'Жарамсыз ogg файлы: $1',
	'timedmedia-more' => 'Көбірек...',
	'timedmedia-dismiss' => 'Жабу',
	'timedmedia-download' => 'Файлды жүктеу',
	'timedmedia-desc-link' => 'Бұл файл туралы',
);

/** Kazakh (Latin) (Қазақша (Latin)) */
$messages['kk-latn'] = array(
	'timedmedia-ogg-short-audio' => 'Ogg $1 dıbıs faýlı, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 beýne faýlı, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 taspa faýlı, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 dıbıs faýlı, uzaqtığı $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 beýne faýlı, uzaqtığı $2, $4 × $5 pïksel, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg quramdı dıbıs/beýne faýlı, $1, uzaqtığı $2, $4 × $5 pïksel, $3 ne barlığı',
	'timedmedia-ogg-long-general' => 'Ogg taspa faýlı, uzaqtığı $2, $3',
	'timedmedia-ogg-long-error' => 'Jaramsız ogg faýlı: $1',
	'timedmedia-more' => 'Köbirek...',
	'timedmedia-dismiss' => 'Jabw',
	'timedmedia-download' => 'Faýldı jüktew',
	'timedmedia-desc-link' => 'Bul faýl twralı',
);

/** Khmer (ភាសាខ្មែរ)
 * @author Chhorran
 * @author Lovekhmer
 * @author T-Rithy
 * @author Thearith
 * @author គីមស៊្រុន
 */
$messages['km'] = array(
	'timedmedia-desc' => 'គាំទ្រចំពោះ Ogg Theora និង Vorbis files, ជាមួយ ឧបករណ៍អាន JavaScript',
	'timedmedia-ogg-short-audio' => 'ឯកសារ សំឡេង Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'ឯកសារវីដេអូ Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'ឯកសារមេឌាOgg $1, $2',
	'timedmedia-ogg-long-audio' => 'ឯកសារសំឡេងប្រភេទOgg $1, រយៈពេល$2 និងទំហំ$3',
	'timedmedia-ogg-long-video' => 'ឯកសារវីដេអូប្រភេទOgg $1, រយៈពេល$2, $4×$5px, $3',
	'timedmedia-ogg-long-multiplexed' => 'ឯកសារអូឌីយ៉ូ/វីដេអូចម្រុះប្រភេទOgg , $1, រយៈពេល$2, $4×$5px, ប្រហែល$3',
	'timedmedia-ogg-long-general' => 'ឯកសារមេឌាប្រភេទOgg, រយៈពេល$2, $3',
	'timedmedia-ogg-long-error' => 'ឯកសារ ogg មិនមាន សុពលភាព ៖ $1',
	'timedmedia-more' => 'បន្ថែម...',
	'timedmedia-dismiss' => 'បិទ',
	'timedmedia-download' => 'ទាញយកឯកសារ',
	'timedmedia-desc-link' => 'អំពីឯកសារនេះ',
);

/** Korean (한국어)
 * @author ITurtle
 * @author Kwj2772
 * @author ToePeu
 */
$messages['ko'] = array(
	'timedmedia-desc' => 'OGG Theora 및 Vorbis 파일 핸들러와 자바스크립트 플레이어',
	'timedmedia-ogg-short-audio' => 'Ogg $1 소리 파일, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 영상 파일, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 미디어 파일, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 소리 파일, 길이 $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 영상 파일, 길이 $2, $4×$5 픽셀, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg 다중 소리/영상 파일, $1, 길이 $2, $4×$5 픽셀, 대략 $3',
	'timedmedia-ogg-long-general' => 'Ogg 미디어 파일, 길이 $2, $3',
	'timedmedia-ogg-long-error' => '잘못된 ogg 파일: $1',
	'timedmedia-no-player-js' => '죄송합니다, 당신의 시스템은 자바스크립트를 지원하지 않거나 지원하는 미디어 플레이어가 설치되어 있지 않습니다.<br />
<a href="$1">미디어 클립을 다운로드</a>하거나, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">미디어 플레이어를 다운로드</a>할 수 있습니다.',
	'timedmedia-more' => '더 보기...',
	'timedmedia-dismiss' => '닫기',
	'timedmedia-download' => '파일 다운로드',
	'timedmedia-desc-link' => '파일 정보',
	'timedmedia-oggThumb-version' => 'OggHandler는 oggThumb 버전 $1 이상을 요구합니다.',
	'timedmedia-oggThumb-failed' => 'oggThumb가 섬네일을 생성하지 못했습니다.',
);

/** Kinaray-a (Kinaray-a)
 * @author Jose77
 */
$messages['krj'] = array(
	'timedmedia-more' => 'Raku pa...',
);

/** Colognian (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'timedmedia-desc' => 'E Projamm (<i lang="en">handler</i>) för Meedije met Zickaandeil
— Viddejos, Tondatteieje, <i lang="en">timedText</i> (Ongertittelle) —
met Ongershtözung för de Fommaate <i lang="en">WebM</i>, <i lang="en">Ogg Theora</i>, <i lang="en">Vorbis</i> un <i lang="en">srt</i>.',
	'timedmedia-ogg-short-audio' => '<i lang="en">Ogg $1</i> Tondatei, $2',
	'timedmedia-ogg-short-video' => '<i lang="en">Ogg $1</i> Viddejodatei, $2',
	'timedmedia-ogg-short-general' => '<i lang="en">Ogg $1</i> Medijedatei, $2',
	'timedmedia-ogg-long-audio' => '<i lang="en">Ogg $1</i> Tondatei fum Ömfang $2, $3',
	'timedmedia-ogg-long-video' => '<i lang="en">Ogg $1</i> Viddejodatei fum Ömfang $2 un {{PLURAL:$4|ein Pixel|$4 Pixelle|kei Pixel}} × {{PLURAL:$5|ei Pixel|$4 Pixelle|kei Pixel}}, $3',
	'timedmedia-ogg-long-multiplexed' => '<i lang="en">Ogg</i> jemultipex Ton- un Viddejodatei, $1, fum Ömfang $2 un {{PLURAL:$4|ein Pixel|$4 Pixelle|kei Pixel}} × {{PLURAL:$5|ei Pixel|$4 Pixelle|kei Pixel}}, $3 ennsjesammp',
	'timedmedia-ogg-long-general' => '<i lang="en">Ogg</i> Medijedatei fum Ömfang $2, $3',
	'timedmedia-ogg-long-error' => 'ene kapodde <i lang="en">Ogg</i> Datei: $1',
	'timedmedia-webm-short-video' => 'En Viddejo-Dattei em WebM-Fommaat',
	'timedmedia-webm-long-video' => 'Ene Viddejo udder en Toondattei em WebM-Fommaat, $1, $2 jruuß, $4 × $5 Pixelle, $3 enßjesammp',
	'timedmedia-no-player-js' => 'Schad, Dinge Brauser hät entweder JavaSkrepp ußjeschalldt udder kein zopaß Projramm zom Afschpelle.<br />Do kanns jäz <a href="$1">dat Stöck eronger laade</a> udder <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">e Afspeller-Projramm eronger laade</a>, öm dat Schtöck en Dingem Brauser afzeschpelle.',
	'timedmedia-more' => 'Enshtelle&nbsp;…',
	'timedmedia-dismiss' => 'Zomaache!',
	'timedmedia-download' => 'Datei erunger lade',
	'timedmedia-play-media' => 'Afshpelle',
	'timedmedia-desc-link' => 'Övver di Datei',
	'timedmedia-oggThumb-version' => 'Dä <code lang="en">OggHandler</code> bruch <code lang="en">oggThumb</code> in dä Version $1 udder hüüter.',
	'timedmedia-oggThumb-failed' => '<code lang="en">oggThumb</code> kunnt kei MiniBelldsche maache.',
	'timedmedia-source-file' => 'Quell-Dattei em $1-Fommaat',
	'timedmedia-source-file-desc' => 'Ojinaal $1-Dattei, $2 × $3 ($4)',
	'timedmedia-derivative-220_200kbs.ogv' => 'En Viddejo-Dattei em OGG-Fommaat met 720p',
	'timedmedia-derivative-desc-220_200kbs.ogv' => 'En Viddejo-Dattei met winnesch Bandbreide (200P) em <i lang="en">Ogg</i>-Fommaat',
	'timedmedia-derivative-desc-360_400kbs.ogv' => 'En Viddejo-Dattei em <i lang="en">Ogg</i>-Fommaat (360p) för ene Dahteshtrohm övver et Nez',
	'timedmedia-derivative-desc-480_600kbs.ogv' => 'En Viddejo-Dattei (met 480p) em <i lang="en">Ogg</i>-Fommaat för ene Dahteshtrohm övver et Nez',
	'timedmedia-derivative-desc-720_VBR.ogv' => 'En Viddejo-Dattei met huhe Qualiteit (met 720p) em <i lang="en">Ogg</i>-Fommaat zom eronger laade',
	'timedmedia-derivative-desc-480_600kbs.webm' => 'Ene WebM Viddejo (met 480p) för ene Dahteshtrohm övver et Nez',
	'timedmedia-derivative-desc720_VBR.webm' => 'En <i lang="en">WebM</i> Viddejo-Dattei met huhe Qualiteit (met 720p) zom eronger laade',
	'timedmedia-subtitle-language' => 'Ongertittele en $1 ($2)',
);

/** Latin (Latina)
 * @author SPQRobin
 * @author UV
 */
$messages['la'] = array(
	'timedmedia-more' => 'Plus…',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Les Meloures
 * @author Robby
 */
$messages['lb'] = array(
	'timedmedia-desc' => "Steierungsprogramm fir Audio, Video an 'timed text', mat Ënnerstëtzung vun de Formater WebM, Ogg Theora, Vorbis, srt",
	'timedmedia-ogg-short-audio' => 'Ogg-$1-Tounfichier, $2',
	'timedmedia-ogg-short-video' => 'Ogg-$1-Videofichier, $2',
	'timedmedia-ogg-short-general' => 'Ogg-$1-Mediefichier, $2',
	'timedmedia-ogg-long-audio' => 'tmh-$1-Tounfichier, Dauer: $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg-$1-Videofichier, Dauer: $2, $4×$5 Pixel, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg-Toun-/Video-Fichier, $1, Dauer: $2, $4×$5 Pixel, $3',
	'timedmedia-ogg-long-general' => 'Ogg Media-Fichier, Dauer $2, $3',
	'timedmedia-ogg-long-error' => 'Ongëltegen Ogg-Fichier: $1',
	'timedmedia-no-player-js' => 'Pardon, Äre Browser huet entweder JavaScript ausgeschalt oder en huet kee Player-Programm deen ënnerstëtzt gëtt.<br />
Dir kënnt <a href="$1"> de Clip eroflueden</a> oder <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">e Player-Programm erofluede</a> fir de Clip an Ärem Browser ze spillen.',
	'timedmedia-more' => 'Méi ...',
	'timedmedia-dismiss' => 'Zoumaachen',
	'timedmedia-download' => 'Fichier eroflueden',
	'timedmedia-desc-link' => 'Iwwer dëse Fichier',
	'timedmedia-oggThumb-version' => "Den OggHandler brauch d'Versioun $1 (oder méi eng nei Versioun) vun OggThumb.",
	'timedmedia-oggThumb-failed' => "oggThumb konnt d'Miniaturbild (thumbnail) net uleeën.",
);

/** Lingua Franca Nova (Lingua Franca Nova)
 * @author Malafaya
 */
$messages['lfn'] = array(
	'timedmedia-more' => 'Plu…',
);

/** Limburgish (Limburgs)
 * @author Matthias
 * @author Ooswesthoesbes
 */
$messages['li'] = array(
	'timedmedia-desc' => "Handelt Ogg Theora- en Vorbis-bestande aaf met 'n JavaScript-mediaspeler",
	'timedmedia-ogg-short-audio' => 'Ogg $1 geluidsbestandj, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 videobestandj, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 mediabestandj, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 geluidsbestandj, lingdje $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 videobestandj, lingdje $2, $4×$5 pixels, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg gemultiplexeerd geluids-/videobestandj, $1, lingdje $2, $4×$5 pixels, $3 totaal',
	'timedmedia-ogg-long-general' => 'Ogg mediabestandj, lingdje $2, $3',
	'timedmedia-ogg-long-error' => 'Óngeljig oggg-bestandj: $1',
	'timedmedia-more' => 'Mieë...',
	'timedmedia-dismiss' => 'Sloet',
	'timedmedia-download' => 'Bestandj downloade',
	'timedmedia-desc-link' => 'Euver dit bestandj',
);

/** Lithuanian (Lietuvių)
 * @author Homo
 * @author Matasg
 */
$messages['lt'] = array(
	'timedmedia-desc' => 'Įrankis groti Ogg Theora ir Vorbis failus su JavaScript grotuvu',
	'timedmedia-ogg-short-audio' => 'Ogg $1 garso byla, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 video byla, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 medija byla, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 garso byla, ilgis $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 video byla, ilgis $2, $4×$5 pikseliai, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg sutankinta audio/video byla, $1, ilgis $2, $4×$5 pikseliai, $3 viso',
	'timedmedia-ogg-long-general' => 'Ogg media byla, ilgis $2, $3',
	'timedmedia-ogg-long-error' => 'Bloga ogg byla: $1',
	'timedmedia-more' => 'Daugiau...',
	'timedmedia-dismiss' => 'Uždaryti',
	'timedmedia-download' => 'Atsisiųsti bylą',
	'timedmedia-desc-link' => 'Apie šią bylą',
);

/** Latvian (Latviešu)
 * @author Xil
 */
$messages['lv'] = array(
	'timedmedia-dismiss' => 'Aizvērt',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 * @author Brest
 */
$messages['mk'] = array(
	'timedmedia-desc' => 'Обработувач за аудио, видео и синхронизиран текст, со поддршка за форматите WebM, Ogg Theora, Vorbis, srt',
	'timedmedia-ogg-short-audio' => 'Ogg $1 звучна податотека, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 видео податотека, $2',
	'timedmedia-ogg-short-general' => 'Мултимедијална податотека Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 звучна податотека, должина $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 видео податотека, должина $2, $4×$5 пиксели, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg мултиплексирана аудио/видео податотека, $1, должина $2, $4×$5 пиксели, $3 вкупно',
	'timedmedia-ogg-long-general' => 'мултимедијална податотека Ogg, должина $2, $3',
	'timedmedia-ogg-long-error' => 'Оштетена ogg податотека: $1',
	'timedmedia-webm-short-video' => 'WebM $1 видеоснимка, $2',
	'timedmedia-webm-long-video' => 'WebM аудио/видео снимка, $1, должина: $2, $4 × $5 пиксели, вкупно $3',
	'timedmedia-no-player-js' => 'Нажалост, вашиот прелистувач или има оневозможено JavaScript, или нема ниту еден поддржан изведувач.<br />
Можете да го <a href="$1">преземете клипот</a> или <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">да преземете изведувач</a> за да ја пуштите снимката во вашиот прелистувач.',
	'timedmedia-more' => 'Повеќе...',
	'timedmedia-dismiss' => 'Затвори',
	'timedmedia-download' => 'Преземи податотека',
	'timedmedia-play-media' => 'Пушти снимка',
	'timedmedia-desc-link' => 'Информации за оваа податотека',
	'timedmedia-oggThumb-version' => 'OggHandler бара oggThumb верзија $1 или понова.',
	'timedmedia-oggThumb-failed' => 'oggThumb не успеа да ја создаде минијатурата.',
	'timedmedia-source-file' => 'Извор на $1',
	'timedmedia-source-file-desc' => 'Изворен $1, $2 × $3 ($4)',
	'timedmedia-derivative-220_200kbs.ogv' => 'Ogg 200P',
	'timedmedia-derivative-desc-220_200kbs.ogv' => 'Нископропусно Ogg-видео (200п)',
	'timedmedia-derivative-360_400kbs.ogv' => 'Ogg 360п',
	'timedmedia-derivative-desc-360_400kbs.ogv' => 'Ogg-видео за емитување (360 пиксели)',
	'timedmedia-derivative-480_600kbs.ogv' => 'Ogg 480п',
	'timedmedia-derivative-desc-480_600kbs.ogv' => 'Ogg-видео за емитување (480 пиксели)',
	'timedmedia-derivative-desc-720_VBR.ogv' => 'Висококвалитетно Ogg-видео (720 пиксели)',
	'timedmedia-derivative-480_600kbs.webm' => 'Висококвалитетен WebM за преземање (720п)',
	'timedmedia-derivative-desc-480_600kbs.webm' => 'Висококвалитетен WebM за преземање (720п)',
	'timedmedia-derivative-desc720_VBR.webm' => 'Висококвалитетен WebM за преземање (720п)',
	'timedmedia-subtitle-language' => '$1 ($2) титлови',
);

/** Malayalam (മലയാളം)
 * @author Praveenp
 * @author Shijualex
 */
$messages['ml'] = array(
	'timedmedia-desc' => 'ജാവാസ്ക്രിപ്റ്റ് പ്ലേയർ ഉപയോഗിച്ച് ഓഗ് തിയോറ, വോർബിസ് പ്രമാണങ്ങൾ കൈകാര്യം ചെയ്യൽ',
	'timedmedia-ogg-short-audio' => 'ഓഗ് $1 ശബ്ദപ്രമാണം, $2',
	'timedmedia-ogg-short-video' => 'ഓഗ് $1 വീഡിയോ പ്രമാണം, $2',
	'timedmedia-ogg-short-general' => 'ഓഗ് $1 മീഡിയ പ്രമാണം, $2',
	'timedmedia-ogg-long-audio' => 'ഓഗ് $1 ശബ്ദ പ്രമാണം, ദൈർഘ്യം $2, $3',
	'timedmedia-ogg-long-video' => 'ഓഗ് $1 വീഡിയോ പ്രമാണം, ദൈർഘ്യം $2, $4×$5 pixels, $3',
	'timedmedia-ogg-long-multiplexed' => 'ഓഗ് മൾട്ടിപ്ലക്സ്‌‌ഡ് ശബ്ദ/ചലച്ചിത്ര പ്രമാണം, $1, ദൈർഘ്യം $2, $4×$5 ബിന്ദു, ആകെക്കൂടി $3',
	'timedmedia-ogg-long-general' => 'ഓഗ് മീഡിയ പ്രമാണം, ദൈർഘ്യം $2, $3',
	'timedmedia-ogg-long-error' => 'അസാധുവായ ഓഗ് പ്രമാണം: $1',
	'timedmedia-more' => 'കൂടുതൽ...',
	'timedmedia-dismiss' => 'അടയ്ക്കുക',
	'timedmedia-download' => 'പ്രമാണം ഡൗൺലോഡ് ചെയ്യുക',
	'timedmedia-desc-link' => 'ഈ പ്രമാണത്തെക്കുറിച്ച്',
	'timedmedia-oggThumb-version' => 'ഓഗ്-തമ്പ് പതിപ്പ് $1 അല്ലെങ്കിൽ പുതിയത് ഓഗ്-ഹാൻഡ്ലറിനാവശ്യമാണ്.',
	'timedmedia-oggThumb-failed' => 'ലഘുചിത്രം സൃഷ്ടിക്കുന്നതിൽ ഓഗ്-തമ്പ് പരാജയപ്പെട്ടു.',
);

/** Marathi (मराठी)
 * @author Kaustubh
 */
$messages['mr'] = array(
	'timedmedia-desc' => 'ऑग थियोरा व वॉर्बिस संचिकांसाठीचा चालक, जावास्क्रीप्ट प्लेयर सकट',
	'timedmedia-ogg-short-audio' => 'ऑग $1 ध्वनी संचिका, $2',
	'timedmedia-ogg-short-video' => 'ऑग $1 चलतचित्र संचिका, $2',
	'timedmedia-ogg-short-general' => 'ऑग $1 मीडिया संचिका, $2',
	'timedmedia-ogg-long-audio' => 'ऑग $1 ध्वनी संचिका, लांबी $2, $3',
	'timedmedia-ogg-long-video' => 'ऑग $1 चलतचित्र संचिका, लांबी $2, $4×$5 पीक्सेल्स, $3',
	'timedmedia-ogg-long-multiplexed' => 'ऑग ध्वनी/चित्र संचिका, $1, लांबी $2, $4×$5 पिक्सेल्स, $3 एकूण',
	'timedmedia-ogg-long-general' => 'ऑग मीडिया संचिका, लांबी $2, $3',
	'timedmedia-ogg-long-error' => 'चुकीची ऑग संचिका: $1',
	'timedmedia-more' => 'आणखी...',
	'timedmedia-dismiss' => 'बंद करा',
	'timedmedia-download' => 'संचिका उतरवा',
	'timedmedia-desc-link' => 'या संचिकेबद्दलची माहिती',
);

/** Malay (Bahasa Melayu)
 * @author Aviator
 */
$messages['ms'] = array(
	'timedmedia-desc' => 'Pengelola fail Ogg Theora dan Vorbis, dengan pemain JavaScript',
	'timedmedia-ogg-short-audio' => 'fail bunyi Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'fail video Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'fail media Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'fail bunyi Ogg $1, tempoh $2, $3',
	'timedmedia-ogg-long-video' => 'fail video Ogg $1, tempoh $2, $4×$5 piksel, $3',
	'timedmedia-ogg-long-multiplexed' => 'fail audio/video multipleks Ogg, $1, tempoh $2, $4×$5 piksel, keseluruhan $3',
	'timedmedia-ogg-long-general' => 'fail media Ogg, tempoh $2, $3',
	'timedmedia-ogg-long-error' => 'Fail Ogg tidak sah: $1',
	'timedmedia-more' => 'Lagi…',
	'timedmedia-dismiss' => 'Tutup',
	'timedmedia-download' => 'Muat turun fail',
	'timedmedia-desc-link' => 'Perihal fail ini',
);

/** Erzya (Эрзянь)
 * @author Botuzhaleny-sodamo
 */
$messages['myv'] = array(
	'timedmedia-desc-link' => 'Те файладонть',
);

/** Nahuatl (Nāhuatl)
 * @author Fluence
 */
$messages['nah'] = array(
	'timedmedia-more' => 'Huehca ōmpa...',
	'timedmedia-download' => 'Tictemōz tlahcuilōlli',
	'timedmedia-desc-link' => 'Inīn tlahcuilōltechcopa',
);

/** Low German (Plattdüütsch)
 * @author Slomox
 */
$messages['nds'] = array(
	'timedmedia-desc' => 'Stüürprogramm för Ogg-Theora- un Vorbis Datein, mitsamt en Afspeler in JavaScript',
	'timedmedia-ogg-short-audio' => 'Ogg-$1-Toondatei, $2',
	'timedmedia-ogg-short-video' => 'Ogg-$1-Videodatei, $2',
	'timedmedia-ogg-short-general' => 'Ogg-$1-Mediendatei, $2',
	'timedmedia-ogg-long-audio' => 'Ogg-$1-Toondatei, $2 lang, $3',
	'timedmedia-ogg-long-video' => 'Ogg-$1-Videodatei, $2 lang, $4×$5 Pixels, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg-Multiplexed-Audio-/Video-Datei, $1, $2 lang, $4×$5 Pixels, $3 alltohoop',
	'timedmedia-ogg-long-general' => 'Ogg-Mediendatei, $2 lang, $3',
	'timedmedia-ogg-long-error' => 'Kaputte Ogg-Datei: $1',
	'timedmedia-more' => 'Mehr...',
	'timedmedia-dismiss' => 'Dichtmaken',
	'timedmedia-download' => 'Datei dalladen',
	'timedmedia-desc-link' => 'Över disse Datei',
);

/** Nedersaksisch (Nedersaksisch)
 * @author Servien
 */
$messages['nds-nl'] = array(
	'timedmedia-desc' => 'Haandelt veur Ogg Theora- en Vorbisbestanen, mit JavaScriptmediaspeuler',
	'timedmedia-ogg-short-audio' => 'Ogg $1 geluudsbestaand, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 videobestaand, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 mediabestaand, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 geluudsbestaand, lengte $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 videobestaand, lengte $2, $4×$5 pixels, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg emultiplexed geluuds-/videobestaand, $1, lengte $2, $4×$5 pixels, $3 totaal',
	'timedmedia-ogg-long-general' => 'Ogg-mediabestaand, lengte $2, $3',
	'timedmedia-ogg-long-error' => 'Ongeldig Ogg-bestaand: $1',
	'timedmedia-more' => 'Meer...',
	'timedmedia-dismiss' => 'Sluten',
	'timedmedia-download' => 'Bestaand binnenhaolen',
	'timedmedia-desc-link' => 'Over dit bestaand',
);

/** Dutch (Nederlands)
 * @author SPQRobin
 * @author Siebrand
 */
$messages['nl'] = array(
	'timedmedia-desc' => 'Handelt audio, video en ondertitels af met ondersteuning voor WebM, Ogg Theora, Vorbis en srt',
	'timedmedia-ogg-short-audio' => 'Ogg $1 geluidsbestand, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 videobestand, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 mediabestand, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 geluidsbestand, lengte $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 video file, lengte $2, $4×$5 pixels, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg gemultiplexed geluids/videobestand, $1, lengte $2, $4×$5 pixels, $3 totaal',
	'timedmedia-ogg-long-general' => 'Ogg mediabestand, lengte $2, $3',
	'timedmedia-ogg-long-error' => 'Ongeldig Ogg-bestand: $1',
	'timedmedia-webm-short-video' => 'WebM $1 videobestand, $2',
	'timedmedia-webm-long-video' => 'WebM audio/videobestand, $1, lengte $2, $4x$5 pixels, $3 totaal',
	'timedmedia-no-player-js' => 'Uw systeem heeft JavaScript uitgeschakeld of er is geen ondersteunde mediaspeler.<br />
U kunt <a href="$1">de clip downloaden</a> of <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">een mediaspeler downloaden</a> om de clip af te spelen in uw browser.',
	'timedmedia-more' => 'Meer…',
	'timedmedia-dismiss' => 'Sluiten',
	'timedmedia-download' => 'Bestand downloaden',
	'timedmedia-play-media' => 'Media afspelen',
	'timedmedia-desc-link' => 'Over dit bestand',
	'timedmedia-oggThumb-version' => 'OggHandler vereist oggThumb versie $1 of hoger.',
	'timedmedia-oggThumb-failed' => 'oggThumb kon geen miniatuur aanmaken.',
	'timedmedia-source-file' => 'Bron van $1',
	'timedmedia-source-file-desc' => 'Origineel $1, $2 × $3 ($4)',
	'timedmedia-derivative-220_200kbs.ogv' => 'Ogg 200P',
	'timedmedia-derivative-desc-220_200kbs.ogv' => 'Lage bandbreedte Ogg video (200P)',
	'timedmedia-derivative-desc-360_400kbs.ogv' => 'Te webstreamen Off video (360P)',
	'timedmedia-derivative-desc-480_600kbs.ogv' => 'Te webstreamen Ogg video (480P)',
	'timedmedia-derivative-desc-720_VBR.ogv' => 'Hoge kwaliteit downloadbare Ogg video (720P)',
	'timedmedia-derivative-desc-480_600kbs.webm' => 'Via web te streamen WebM (480P)',
	'timedmedia-derivative-desc720_VBR.webm' => 'Hoge kwaliteit downloadbare WebM (720P)',
	'timedmedia-subtitle-language' => 'Ondertitels in $1 ($2)',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Eirik
 * @author Harald Khan
 */
$messages['nn'] = array(
	'timedmedia-desc' => 'Gjer at Ogg Theora- og Ogg Vorbis-filer kan verta køyrte ved hjelp av JavaScript-avspelar.',
	'timedmedia-ogg-short-audio' => 'Ogg $1-lydfil, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1-videofil, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1-mediafil, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1-lydfil, lengd $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1-videofil, lengd $2, $4×$5 pikslar, $3',
	'timedmedia-ogg-long-multiplexed' => 'Samansett ogg lyd-/videofil, $1, lengd $2, $4×$5 pikslar, $3 til saman',
	'timedmedia-ogg-long-general' => 'Ogg mediafil, lengd $2, $3',
	'timedmedia-ogg-long-error' => 'Ugyldig Ogg-fil: $1',
	'timedmedia-more' => 'Meir...',
	'timedmedia-dismiss' => 'Lat att',
	'timedmedia-download' => 'Last ned fila',
	'timedmedia-desc-link' => 'Om denne fila',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 * @author Laaknor
 * @author Nghtwlkr
 */
$messages['no'] = array(
	'timedmedia-desc' => 'Gjør at Ogg Theora- og Ogg Vorbis-filer kan kjøres med hjelp av JavaScript-avspiller.',
	'timedmedia-ogg-short-audio' => 'Ogg $1 lydfil, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 videofil, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 mediefil, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 lydfil, lengde $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 videofil, lengde $2, $4×$5 piksler, $3',
	'timedmedia-ogg-long-multiplexed' => 'Sammensatt ogg lyd-/videofil, $1, lengde $2, $4×$5 piksler, $3 til sammen',
	'timedmedia-ogg-long-general' => 'Ogg mediefil, lengde $2, $3',
	'timedmedia-ogg-long-error' => 'Ugyldig Ogg-fil: $1',
	'timedmedia-no-player-js' => 'Beklager, nettleseren din har enten deaktivert JavaScript eller har ingen støttet spiller.<br />
Du kan <a href="$1">laste ned klippet</a> eller <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">laste ned en spiller</a> for å spille av klippet i nettleseren din.',
	'timedmedia-more' => 'Mer …',
	'timedmedia-dismiss' => 'Lukk',
	'timedmedia-download' => 'Last ned fil',
	'timedmedia-desc-link' => 'Om denne filen',
	'timedmedia-oggThumb-version' => 'OggHandler krever oggThumb versjon $1 eller senere.',
	'timedmedia-oggThumb-failed' => 'oggThumb klarte ikke å opprette miniatyrbildet.',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'timedmedia-desc' => 'Supòrt pels fichièrs Ogg Theora e Vorbis, amb un lector Javascript',
	'timedmedia-ogg-short-audio' => 'Fichièr son Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Fichièr vidèo Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Fichièr mèdia Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'Fichièr son Ogg $1, durada $2, $3',
	'timedmedia-ogg-long-video' => 'Fichièr vidèo Ogg $1, durada $2, $4×$5 pixèls, $3',
	'timedmedia-ogg-long-multiplexed' => 'Fichièr multiplexat àudio/vidèo Ogg, $1, durada $2, $4×$5 pixèls, $3',
	'timedmedia-ogg-long-general' => 'Fichièr mèdia Ogg, durada $2, $3',
	'timedmedia-ogg-long-error' => 'Fichièr Ogg invalid : $1',
	'timedmedia-more' => 'Mai…',
	'timedmedia-dismiss' => 'Tampar',
	'timedmedia-download' => 'Telecargar lo fichièr',
	'timedmedia-desc-link' => "A prepaus d'aqueste fichièr",
);

/** Ossetic (Иронау)
 * @author Amikeco
 */
$messages['os'] = array(
	'timedmedia-more' => 'Фылдæр…',
	'timedmedia-download' => 'Файл æрбавгæн',
);

/** Punjabi (ਪੰਜਾਬੀ)
 * @author Gman124
 */
$messages['pa'] = array(
	'timedmedia-more' => 'ਹੋਰ...',
);

/** Deitsch (Deitsch)
 * @author Xqt
 */
$messages['pdc'] = array(
	'timedmedia-more' => 'Mehr…',
	'timedmedia-download' => 'Feil runnerlaade',
);

/** Polish (Polski)
 * @author Derbeth
 * @author Leinad
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'timedmedia-desc' => 'Obsługa plików audio, wideo i napisów filmowych w formatach WebM, Ogg Theora, Vorbis i srt',
	'timedmedia-ogg-short-audio' => 'Plik dźwiękowy Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Plik wideo Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Plik multimedialny Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'plik dźwiękowy Ogg $1, długość $2, $3',
	'timedmedia-ogg-long-video' => 'plik wideo Ogg $1, długość $2, rozdzielczość $4×$5, $3',
	'timedmedia-ogg-long-multiplexed' => 'plik audio/wideo Ogg, $1, długość $2, rozdzielczość $4×$5, ogółem $3',
	'timedmedia-ogg-long-general' => 'plik multimedialny Ogg, długość $2, $3',
	'timedmedia-ogg-long-error' => 'niepoprawny plik Ogg: $1',
	'timedmedia-no-player-js' => 'Niestety, Twoja przeglądarka ma wyłączoną obsługę JavaScript lub nie wspiera odtwarzania.<br />
Możesz <a href="$1">pobrać plik</a> lub <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">pobrać odtwarzacz</a> pozwalający oglądać wideo w przeglądarce.',
	'timedmedia-more' => 'Więcej...',
	'timedmedia-dismiss' => 'Zamknij',
	'timedmedia-download' => 'Pobierz plik',
	'timedmedia-desc-link' => 'Właściwości pliku',
	'timedmedia-oggThumb-version' => 'OggHandler wymaga oggThumb w wersji $1 lub późniejszej.',
	'timedmedia-oggThumb-failed' => 'oggThumb nie udało się utworzyć miniaturki.',
);

/** Piedmontese (Piemontèis)
 * @author Borichèt
 * @author Bèrto 'd Sèra
 * @author Dragonòt
 */
$messages['pms'] = array(
	'timedmedia-desc' => "Gestor për ij mojen ch'a dësfilo (filmà, sonor, test sincronisà), con riprodussion an Ogg Theora/Vorbis",
	'timedmedia-ogg-short-audio' => 'Registrassion Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Film Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Archivi Multimojen Ogg $1, $2',
	'timedmedia-ogg-long-audio' => "Registrassion Ogg $1, ch'a dura $2, $3",
	'timedmedia-ogg-long-video' => "Film Ogg $1, ch'a dura $2, formà $4×$5 px, $3",
	'timedmedia-ogg-long-multiplexed' => "Archivi audio/video multiplessà Ogg, $1, ch'a dura $2, formà $4×$5 px, $3 an tut",
	'timedmedia-ogg-long-general' => "Archivi multimojen Ogg, ch'a dura $2, $3",
	'timedmedia-ogg-long-error' => 'Archivi ogg nen bon: $1',
	'timedmedia-no-player-js' => 'Darmagi, sò navigador a l\'ha JavaScript disabilità o a supòrta pa ël riprodutor.<br />
A peul <a href="$1">dëscarié la senëtta</a> o <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">dëscarié un riprodutor</a> për visualisé la senëtta su sò navigador.',
	'timedmedia-more' => 'Dë pì...',
	'timedmedia-dismiss' => 'sëré',
	'timedmedia-download' => "Dëscarié l'archivi",
	'timedmedia-desc-link' => "Rësgoard a st'archivi",
	'timedmedia-oggThumb-version' => "OggHandler a ciama la version $1 d'oggThumb o pi agiornà.",
	'timedmedia-oggThumb-failed' => "oggThumb a l'ha falì a creé la figurin-a.",
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'timedmedia-ogg-short-audio' => 'Ogg $1 غږيزه دوتنه، $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 ويډيويي دوتنه، $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 رسنيزه دوتنه، $2',
	'timedmedia-more' => 'نور...',
	'timedmedia-dismiss' => 'تړل',
	'timedmedia-download' => 'دوتنه ښکته کول',
	'timedmedia-desc-link' => 'د همدې دوتنې په اړه',
);

/** Portuguese (Português)
 * @author 555
 * @author Giro720
 * @author Hamilton Abreu
 * @author Malafaya
 * @author Waldir
 */
$messages['pt'] = array(
	'timedmedia-desc' => 'Tratamento de áudio, vídeo e legendagem, nos formatos WebM, Ogg Theora, Vorbis, srt',
	'timedmedia-ogg-short-audio' => 'Áudio Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Vídeo Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Multimédia Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'Áudio Ogg $1, $2 de duração, $3',
	'timedmedia-ogg-long-video' => 'Vídeo Ogg $1, $2 de duração, $4×$5 pixels, $3',
	'timedmedia-ogg-long-multiplexed' => 'Áudio/vídeo Ogg multifacetado, $1, $2 de duração, $4×$5 pixels, $3 no todo',
	'timedmedia-ogg-long-general' => 'Multimédia Ogg, $2 de duração, $3',
	'timedmedia-ogg-long-error' => 'Ficheiro ogg inválido: $1',
	'timedmedia-webm-short-video' => 'Vídeo WebM $1, $2',
	'timedmedia-webm-long-video' => 'Áudio/vídeo WebM, $1, $2 de duração, $4 × $5 pixels, $3 no todo',
	'timedmedia-no-player-js' => 'Desculpe, mas ou o seu browser está com o JavaScript desactivado ou não tem nenhum dos leitores suportados.<br />
Pode fazer o <a href="$1">download do vídeo</a> ou o <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">download de um leitor</a> para assistir ao vídeo no seu browser.',
	'timedmedia-more' => 'Mais...',
	'timedmedia-dismiss' => 'Fechar',
	'timedmedia-download' => 'Fazer download do ficheiro',
	'timedmedia-play-media' => 'Reproduzir conteúdo',
	'timedmedia-desc-link' => 'Sobre este ficheiro',
	'timedmedia-oggThumb-version' => 'O oggHandler requer o oggThumb versão $1 ou posterior.',
	'timedmedia-oggThumb-failed' => 'O oggThumb não conseguiu criar a miniatura.',
	'timedmedia-source-file' => 'Fonte $1',
	'timedmedia-source-file-desc' => 'Original $1, $2 × $3 ($4)',
	'timedmedia-derivative-220_200kbs.ogv' => 'Ogg 200P',
	'timedmedia-derivative-desc-220_200kbs.ogv' => 'Vídeo Ogg de baixa largura de banda (200P)',
	'timedmedia-derivative-desc-360_400kbs.ogv' => 'Vídeo Ogg para web streaming (360P)',
	'timedmedia-derivative-desc-480_600kbs.ogv' => 'Vídeo Ogg para web streaming (480P)',
	'timedmedia-derivative-desc-720_VBR.ogv' => 'Vídeo Ogg de alta qualidade para download (720 P)',
	'timedmedia-derivative-desc-480_600kbs.webm' => 'WebM para web streaming (480P)',
	'timedmedia-derivative-desc720_VBR.webm' => 'WebM de alta qualidade para download (720P)',
	'timedmedia-subtitle-language' => 'Legendas em $1 ($2)',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Eduardo.mps
 * @author Giro720
 */
$messages['pt-br'] = array(
	'timedmedia-desc' => 'Manipulador para arquivos Ogg Theora e Vorbis, com reprodutor JavaScript',
	'timedmedia-ogg-short-audio' => 'Arquivo de áudio Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Arquivo de vídeo Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Arquivo multimídia Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'Arquivo de Áudio Ogg $1, $2 de duração, $3',
	'timedmedia-ogg-long-video' => 'Vídeo Ogg $1, $2 de duração, $4×$5 pixels, $3',
	'timedmedia-ogg-long-multiplexed' => 'Áudio/vídeo Ogg multifacetado, $1, $2 de duração, $4×$5 pixels, $3 no todo',
	'timedmedia-ogg-long-general' => 'Multimídia Ogg, $2 de duração, $3',
	'timedmedia-ogg-long-error' => 'Ficheiro ogg inválido: $1',
	'timedmedia-no-player-js' => 'Desculpe, seu navegador ou está com JavaScript desabilitado ou não tem nenhum "player" suportado.<br />
Você pode <a href="$1">descarregar o clipe</a> ou <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">descarregar um "player"</a> para executar o clipe em seu navegador.',
	'timedmedia-more' => 'Mais...',
	'timedmedia-dismiss' => 'Fechar',
	'timedmedia-download' => 'Descarregar arquivo',
	'timedmedia-desc-link' => 'Sobre este arquivo',
	'timedmedia-oggThumb-version' => 'O oggHandler requer o oggThumb versão $1 ou posterior.',
	'timedmedia-oggThumb-failed' => 'O oggThumb não conseguiu criar a miniatura.',
);

/** Quechua (Runa Simi)
 * @author AlimanRuna
 */
$messages['qu'] = array(
	'timedmedia-more' => 'Astawan...',
	'timedmedia-dismiss' => "Wichq'ay",
	'timedmedia-download' => 'Willañiqita chaqnamuy',
	'timedmedia-desc-link' => 'Kay willañiqimanta',
);

/** Romanian (Română)
 * @author KlaudiuMihaila
 * @author Mihai
 * @author Minisarm
 * @author Stelistcristi
 */
$messages['ro'] = array(
	'timedmedia-ogg-short-audio' => 'Fișier audio ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Fișier video ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Fișier media ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'Fișier audio ogg $1, lungime $2, $3',
	'timedmedia-ogg-long-video' => 'Fișier video ogg $1, lungime $2, $4×$5 pixeli, $3',
	'timedmedia-ogg-long-multiplexed' => 'Fișier multiplexat audio/video ogg, $1, lungime $2, $4×$5 pixeli, $3',
	'timedmedia-ogg-long-general' => 'Fișier media ogg, lungime $2, $3',
	'timedmedia-ogg-long-error' => 'Fișier ogg incorect: $1',
	'timedmedia-more' => 'Mai mult…',
	'timedmedia-dismiss' => 'Închide',
	'timedmedia-download' => 'Descarcă fișier',
	'timedmedia-desc-link' => 'Despre acest fișier',
);

/** Tarandíne (Tarandíne)
 * @author Joetaras
 */
$messages['roa-tara'] = array(
	'timedmedia-desc' => "Gestore pe le file Ogg Theora e Vorbis, cu 'nu programme de riproduzione JavaScript",
	'timedmedia-ogg-short-audio' => 'File audie Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'File video Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'File media Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'File audie Ogg $1, lunghezze $2, $3',
	'timedmedia-ogg-long-video' => 'File video Ogg $1, lunghezze $2, $4 x $5 pixel, $3',
	'timedmedia-ogg-long-multiplexed' => 'File multiplexed audie e video Ogg $1, lunghezze $2, $4 x $5 pixel, $3 in totale',
	'timedmedia-ogg-long-general' => 'File media Ogg, lunghezze $2, $3',
	'timedmedia-ogg-long-error' => 'Ogg file invalide: $1',
	'timedmedia-more' => 'De cchiù...',
	'timedmedia-dismiss' => 'Chiude',
	'timedmedia-download' => 'Scareche stu file',
	'timedmedia-desc-link' => "'Mbormaziune sus a stu file",
	'timedmedia-oggThumb-version' => "OggHandler vole 'a versine de oggThumb $1 o cchiù ierte.",
	'timedmedia-oggThumb-failed' => 'oggThumn ha fallite a ccrejà le miniature.',
);

/** Russian (Русский)
 * @author Ahonc
 * @author Kv75
 * @author MaxSem
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'timedmedia-desc' => 'Обработчик файлов Ogg Theora и Vorbis с использованием JavaScript-проигрывателя',
	'timedmedia-ogg-short-audio' => 'Звуковой файл Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Видео-файл Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Медиа-файл Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'звуковой файл Ogg $1, длительность $2, $3',
	'timedmedia-ogg-long-video' => 'видео-файл Ogg $1, длительность $2, $4×$5 пикселов, $3',
	'timedmedia-ogg-long-multiplexed' => 'мультиплексный аудио/видео-файл Ogg, $1, длительность $2, $4×$5 пикселов, $3 всего',
	'timedmedia-ogg-long-general' => 'медиа-файл Ogg, длительность $2, $3',
	'timedmedia-ogg-long-error' => 'неправильный Ogg-файл: $1',
	'timedmedia-no-player-js' => 'К сожалению, в вашем браузере отключён JavaScript, или не имеется требуемого проигрывателя.<br />
Вы можете <a href="$1">загрузить ролик</a> или <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">загрузить проигрыватель</a> для воспроизведения ролика в браузере.',
	'timedmedia-more' => 'Больше…',
	'timedmedia-dismiss' => 'Скрыть',
	'timedmedia-download' => 'Загрузить файл',
	'timedmedia-desc-link' => 'Информация об этом файле',
	'timedmedia-oggThumb-version' => 'OggHandler требует oggThumb версии $1 или более поздней.',
	'timedmedia-oggThumb-failed' => 'oggThumb не удалось создать миниатюру.',
);

/** Rusyn (Русиньскый)
 * @author Gazeb
 */
$messages['rue'] = array(
	'timedmedia-more' => 'Веце...',
	'timedmedia-dismiss' => 'Заперти',
);

/** Yakut (Саха тыла)
 * @author HalanTul
 */
$messages['sah'] = array(
	'timedmedia-desc' => 'Обработчик файлов Ogg Theora и Vorbis с использованием JavaScript-проигрывателя',
	'timedmedia-ogg-short-audio' => 'Звуковой файл Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Видео-файл Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Медиа-файл Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'звуковой файл Ogg $1, уһуна $2, $3',
	'timedmedia-ogg-long-video' => 'видео-файл Ogg $1, уһуна $2, $4×$5 пииксэллээх, $3',
	'timedmedia-ogg-long-multiplexed' => 'мультиплексный аудио/видео-файл Ogg, $1, уһуна $2, $4×$5 пииксэллээх, барыта $3',
	'timedmedia-ogg-long-general' => 'медиа-файл Ogg, уһуна $2, $3',
	'timedmedia-ogg-long-error' => 'сыыһа Ogg-файл: $1',
	'timedmedia-more' => 'Өссө...',
	'timedmedia-dismiss' => 'Кистээ/сап',
	'timedmedia-download' => 'Билэни хачайдаа',
	'timedmedia-desc-link' => 'Бу билэ туһунан',
);

/** Samogitian (Žemaitėška)
 * @author Hugo.arg
 */
$messages['sgs'] = array(
	'timedmedia-download' => 'Atsėsiōstė faila',
);

/** Sinhala (සිංහල)
 * @author නන්දිමිතුරු
 */
$messages['si'] = array(
	'timedmedia-desc' => 'Ogg Theora සහ Vorbis ගොනු සඳහා හසුරුවනය, ජාවාස්ක්‍රිප්ට් ප්ලේයර් සමඟ',
	'timedmedia-ogg-short-audio' => 'Ogg $1 ශ්‍රව්‍ය ගොනුව, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 දෘශ්‍ය ගොනුව, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 මාධ්‍ය ගොනුව, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 ශ්‍රව්‍ය ගොනුව, ප්‍රවර්තනය $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 දෘශ්‍ය ගොනුව, ප්‍රවර්තනය $2, $4×$5 පික්සල්, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg බහුපථකාරක ශ්‍රව්‍ය/දෘශ්‍ය ගොනුව, $1, ප්‍රවර්තනය $2, $4×$5 පික්සල්, $3 සමස්ත',
	'timedmedia-ogg-long-general' => 'Ogg මාධ්‍ය ගොනුව, ප්‍රවර්තනය $2, $3',
	'timedmedia-ogg-long-error' => 'අනීතික ogg ගොනුව: $1',
	'timedmedia-more' => 'ඉතිරිය…',
	'timedmedia-dismiss' => 'වසන්න',
	'timedmedia-download' => 'ගොනුව බා ගන්න',
	'timedmedia-desc-link' => 'මෙම ගොනුව පිළිබඳ',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'timedmedia-desc' => 'Obsluha súborov Ogg Theora a Vorbis s JavaScriptovým prehrávačom',
	'timedmedia-ogg-short-audio' => 'Zvukový súbor ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Video súbor ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Multimediálny súbor ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'Zvukový súbor ogg $1, dĺžka $2, $3',
	'timedmedia-ogg-long-video' => 'Video súbor ogg $1, dĺžka $2, $4×$5 pixelov, $3',
	'timedmedia-ogg-long-multiplexed' => 'Multiplexovaný zvukový/video súbor ogg, $1, dĺžka $2, $4×$5 pixelov, $3 celkom',
	'timedmedia-ogg-long-general' => 'Multimediálny súbor ogg, dĺžka $2, $3',
	'timedmedia-ogg-long-error' => 'Neplatný súbor ogg: $1',
	'timedmedia-more' => 'viac...',
	'timedmedia-dismiss' => 'Zatvoriť',
	'timedmedia-download' => 'Stiahnuť súbor',
	'timedmedia-desc-link' => 'O tomto súbore',
);

/** Slovenian (Slovenščina)
 * @author Dbc334
 */
$messages['sl'] = array(
	'timedmedia-desc' => 'Upravljavec zvoka, videa in časovnega besedila s podporo oblikam WebM, Ogg Theora, Vorbis, srt',
	'timedmedia-ogg-short-audio' => 'Zvočna datoteka Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Videodatoteka Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Predstavnostna datoteka Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'zvočna datoteka ogg $1, dolžine $2, $3',
	'timedmedia-ogg-long-video' => 'videodatoteka ogg $1, dolžine $2, $4 × $5 pik, $3',
	'timedmedia-ogg-long-multiplexed' => 'multipleksna zvočna/videodatoteka ogg, $1, dolžina $2, $4 × $5 pik, $3 skupno',
	'timedmedia-ogg-long-general' => 'predstavnostna datoteka Ogg, dolžina $2, $3',
	'timedmedia-ogg-long-error' => 'Neveljavna datoteka Ogg: $1',
	'timedmedia-webm-short-video' => 'Videodatoteka WebM $1, $2',
	'timedmedia-webm-long-video' => 'zvočna/videodatoteka WebM, $1, dolžina $2, $4 × $5 pik, $3 skupno',
	'timedmedia-no-player-js' => 'Oprostite, vaš brskalnik ima ali onemogočen JavaScript ali pa nima podprtega predvajalnika.<br />
Lahko <a href="$1">prenesete posnetek</a> ali <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">prenesete predvajalnik</a> za predvajanje posnetka v vašem brskalniku.',
	'timedmedia-more' => 'Več ...',
	'timedmedia-dismiss' => 'Zapri',
	'timedmedia-download' => 'Prenesi datoteko',
	'timedmedia-desc-link' => 'O datoteki',
	'timedmedia-oggThumb-version' => 'OggHandler potrebuje oggThumb različice $1 ali višje.',
	'timedmedia-oggThumb-failed' => 'oggThumb ni uspel ustvariti predogledne sličice.',
	'timedmedia-derivative-desc-220_200kbs.ogv' => 'Video Ogg za majhno pasovno širino (200P)',
	'timedmedia-derivative-desc-360_400kbs.ogv' => 'Video Ogg za pretakanje preko spleta (360P)',
	'timedmedia-derivative-desc-480_600kbs.ogv' => 'Video Ogg za pretakanje preko spleta (480P)',
	'timedmedia-derivative-desc-720_VBR.ogv' => 'Visoko ločljivostni video Ogg za prenos (720P)',
);

/** Albanian (Shqip)
 * @author Dori
 */
$messages['sq'] = array(
	'timedmedia-ogg-short-audio' => 'Skedë zanore Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Skedë pamore Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Skedë mediatike Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'Skedë zanore Ogg $1, kohëzgjatja $2, $3',
	'timedmedia-ogg-long-video' => 'Skedë pamore Ogg $1, kohëzgjatja $2, $4×$5 pixel, $3',
	'timedmedia-more' => 'Më shumë...',
	'timedmedia-dismiss' => 'Mbylle',
	'timedmedia-download' => 'Shkarko skedën',
	'timedmedia-desc-link' => 'Rreth kësaj skede',
);

/** Serbian Cyrillic ekavian (‪Српски (ћирилица)‬)
 * @author Millosh
 * @author Sasa Stefanovic
 * @author Михајло Анђелковић
 */
$messages['sr-ec'] = array(
	'timedmedia-desc' => 'Руковаоц ogg Теора и Ворбис фајловима са јаваскрипт плејером',
	'timedmedia-ogg-short-audio' => 'Ogg $1 звучни фајл, $2.',
	'timedmedia-ogg-short-video' => 'Ogg $1 видео фајл, $2.',
	'timedmedia-ogg-short-general' => 'Ogg $1 медијски фајл, $2.',
	'timedmedia-ogg-long-audio' => 'Ogg $1 звучни фајл, дужина $2, $3.',
	'timedmedia-ogg-long-video' => 'Ogg $1 видео фајл, дужина $2, $4×$5 пиксела, $3.',
	'timedmedia-ogg-long-multiplexed' => 'Ogg мултиплексовани аудио/видео фајл, $1, дужина $2, $4×$5 пиксела, $3 укупно.',
	'timedmedia-ogg-long-general' => 'Ogg медијски фајл, дужина $2, $3.',
	'timedmedia-ogg-long-error' => 'Лош ogg фајл: $1.',
	'timedmedia-more' => 'Више...',
	'timedmedia-dismiss' => 'Затвори',
	'timedmedia-download' => 'Преузми фајл',
	'timedmedia-desc-link' => 'О овом фајлу',
);

/** Serbian Latin ekavian (‪Srpski (latinica)‬)
 * @author Michaello
 */
$messages['sr-el'] = array(
	'timedmedia-desc' => 'Rukovaoc ogg Teora i Vorbis fajlovima sa javaskript plejerom',
	'timedmedia-ogg-short-audio' => 'Ogg $1 zvučni fajl, $2.',
	'timedmedia-ogg-short-video' => 'Ogg $1 video fajl, $2.',
	'timedmedia-ogg-short-general' => 'Ogg $1 medijski fajl, $2.',
	'timedmedia-ogg-long-audio' => 'Ogg $1 zvučni fajl, dužina $2, $3.',
	'timedmedia-ogg-long-video' => 'Ogg $1 video fajl, dužina $2, $4×$5 piksela, $3.',
	'timedmedia-ogg-long-multiplexed' => 'Ogg multipleksovani audio/video fajl, $1, dužina $2, $4×$5 piksela, $3 ukupno.',
	'timedmedia-ogg-long-general' => 'Ogg medijski fajl, dužina $2, $3.',
	'timedmedia-ogg-long-error' => 'Loš ogg fajl: $1.',
	'timedmedia-more' => 'Više...',
	'timedmedia-dismiss' => 'Zatvori',
	'timedmedia-download' => 'Preuzmi fajl',
	'timedmedia-desc-link' => 'O ovom fajlu',
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'timedmedia-desc' => 'Stjuurengsprogramm foar Ogg Theora- un Vorbis-Doatäie, inklusive n JavaScript-Ouspielsoftware',
	'timedmedia-ogg-short-audio' => 'Ogg-$1-Audiodoatäi, $2',
	'timedmedia-ogg-short-video' => 'Ogg-$1-Videodoatäi, $2',
	'timedmedia-ogg-short-general' => 'Ogg-$1-Mediadoatäi, $2',
	'timedmedia-ogg-long-audio' => 'Ogg-$1-Audiodoatäi, Loangte: $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg-$1-Videodoatäi, Loangte: $2, $4×$5 Pixel, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg-Audio-/Video-Doatäi, $1, Loangte: $2, $4×$5 Pixel, $3',
	'timedmedia-ogg-long-general' => 'Ogg-Mediadoatäi, Loangte: $2, $3',
	'timedmedia-ogg-long-error' => 'Uungultige Ogg-Doatäi: $1',
	'timedmedia-more' => 'Optione …',
	'timedmedia-dismiss' => 'Sluute',
	'timedmedia-download' => 'Doatäi spiekerje',
	'timedmedia-desc-link' => 'Uur disse Doatäi',
);

/** Sundanese (Basa Sunda)
 * @author Kandar
 */
$messages['su'] = array(
	'timedmedia-ogg-short-audio' => 'Koropak sora $1 ogg, $2',
	'timedmedia-ogg-short-video' => 'Koropak vidéo $1 ogg, $2',
	'timedmedia-ogg-short-general' => 'Koropak média $1 ogg, $2',
	'timedmedia-ogg-long-audio' => 'Koropak sora $1 ogg, lilana $2, $3',
	'timedmedia-ogg-long-video' => 'Koropak vidéo $1 ogg, lilana $2, $4×$5 piksel, $3',
	'timedmedia-ogg-long-multiplexed' => 'Koropak sora/vidéo ogg multipléks, $1, lilana $2, $4×$5 piksel, $3 gembleng',
	'timedmedia-ogg-long-general' => 'Koropak média ogg, lilana $2, $3',
	'timedmedia-ogg-long-error' => 'Koropak ogg teu valid: $1',
	'timedmedia-more' => 'Lianna...',
	'timedmedia-dismiss' => 'Tutup',
	'timedmedia-download' => 'Bedol',
	'timedmedia-desc-link' => 'Ngeunaan ieu koropak',
);

/** Swedish (Svenska)
 * @author Jon Harald Søby
 * @author Lejonel
 * @author Rotsee
 * @author Skalman
 */
$messages['sv'] = array(
	'timedmedia-desc' => 'Stöder filtyperna Ogg Theora och Ogg Vorbis med en JavaScript-baserad mediaspelare',
	'timedmedia-ogg-short-audio' => 'Ogg $1 ljudfil, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 videofil, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 mediafil, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 ljudfil, längd $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 videofil, längd $2, $4×$5 pixel, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg multiplexad ljud/video-fil, $1, längd $2, $4×$5 pixel, $3 totalt',
	'timedmedia-ogg-long-general' => 'Ogg mediafil, längd $2, $3',
	'timedmedia-ogg-long-error' => 'Felaktig Ogg-fil: $1',
	'timedmedia-more' => 'Mer...',
	'timedmedia-dismiss' => 'Stäng',
	'timedmedia-download' => 'Ladda ner filen',
	'timedmedia-desc-link' => 'Om filen',
);

/** Telugu (తెలుగు)
 * @author Kiranmayee
 * @author Veeven
 * @author వైజాసత్య
 */
$messages['te'] = array(
	'timedmedia-ogg-short-audio' => 'Ogg $1 శ్రావ్యక ఫైలు, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 వీడియో ఫైలు, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 మీడియా ఫైలు, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 శ్రవణ ఫైలు, నిడివి $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 వీడియో ఫైలు, నిడివి $2, $4×$5 పిక్సెళ్ళు, $3',
	'timedmedia-ogg-long-multiplexed' => 'ఓగ్ మల్టిప్లెక్సుడ్ శ్రవణ/దృశ్యక ఫైలు, $1, నిడివి $2, $4×$5 పిక్సెళ్ళు, $3 మొత్తం',
	'timedmedia-ogg-long-general' => 'Ogg మీడియా ఫైలు, నిడివి $2, $3',
	'timedmedia-ogg-long-error' => 'తప్పుడు ogg ఫైలు: $1',
	'timedmedia-more' => 'మరిన్ని...',
	'timedmedia-dismiss' => 'మూసివేయి',
	'timedmedia-download' => 'ఫైలుని దిగుమతి చేసుకోండి',
	'timedmedia-desc-link' => 'ఈ ఫైలు గురించి',
);

/** Tajik (Cyrillic) (Тоҷикӣ (Cyrillic))
 * @author Ibrahim
 */
$messages['tg-cyrl'] = array(
	'timedmedia-desc' => 'Ба дастгирандае барои парвандаҳои  Ogg Theora ва Vorbis, бо пахшкунандаи JavaScript',
	'timedmedia-ogg-short-audio' => 'Ogg $1 парвандаи савтӣ, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 парвандаи наворӣ, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 парвандаи расона, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 парвандаи савтӣ, тӯл $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 парвандаи наворӣ, тӯл $2, $4×$5 пикселҳо, $3',
	'timedmedia-ogg-long-multiplexed' => 'Парвандаи Ogg савтӣ/наворӣ печида, $1, тӯл $2, $4×$5 пикселҳо, дар маҷмӯъ $3',
	'timedmedia-ogg-long-general' => 'Парвандаи расонаи Ogg, тӯл $2, $3',
	'timedmedia-ogg-long-error' => 'Парвандаи ғайримиҷози ogg: $1',
	'timedmedia-more' => 'Бештар...',
	'timedmedia-dismiss' => 'Бастан',
	'timedmedia-download' => 'Боргирии парванда',
	'timedmedia-desc-link' => 'Дар бораи ин парванда',
);

/** Tajik (Latin) (Тоҷикӣ (Latin))
 * @author Liangent
 */
$messages['tg-latn'] = array(
	'timedmedia-desc' => 'Ba dastgirandae baroi parvandahoi  Ogg Theora va Vorbis, bo paxşkunandai JavaScript',
	'timedmedia-ogg-short-audio' => 'Ogg $1 parvandai savtī, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 parvandai navorī, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 parvandai rasona, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 parvandai savtī, tūl $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 parvandai navorī, tūl $2, $4×$5 pikselho, $3',
	'timedmedia-ogg-long-multiplexed' => "Parvandai Ogg savtī/navorī pecida, $1, tūl $2, $4×$5 pikselho, dar maçmū' $3",
	'timedmedia-ogg-long-general' => 'Parvandai rasonai Ogg, tūl $2, $3',
	'timedmedia-ogg-long-error' => 'Parvandai ƣajrimiçozi ogg: $1',
	'timedmedia-more' => 'Beştar...',
	'timedmedia-dismiss' => 'Bastan',
	'timedmedia-download' => 'Borgiriji parvanda',
	'timedmedia-desc-link' => 'Dar borai in parvanda',
);

/** Turkmen (Türkmençe)
 * @author Hanberke
 */
$messages['tk'] = array(
	'timedmedia-desc' => 'Ogg Theora we Vorbis faýllary üçin işleýji, JavaScript pleýeri bilen bilelikde',
	'timedmedia-ogg-short-audio' => 'Ogg $1 ses faýly, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 wideo faýly, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 media faýly, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 ses faýly, uzynlyk $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 wideo faýly, uzynlyk $2, $4×$5 piksel, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg multipleks audio/wideo faýly, $1, uzynlyk $2, $4×$5 piksel, $3 jemi',
	'timedmedia-ogg-long-general' => 'Ogg media faýly, uzynlyk $2, $3',
	'timedmedia-ogg-long-error' => 'Nädogry ogg faýly: $1',
	'timedmedia-more' => 'Has köp...',
	'timedmedia-dismiss' => 'Ýap',
	'timedmedia-download' => 'Faýl düşür',
	'timedmedia-desc-link' => 'Bu faýl hakda',
);

/** Tagalog (Tagalog)
 * @author AnakngAraw
 */
$messages['tl'] = array(
	'timedmedia-desc' => 'Tagahawak para sa mga talaksang Ogg Theora at Vorbis, na may panugtog/pampaandar na JavaScript',
	'timedmedia-ogg-short-audio' => '$1 na talaksang pangtunog ng Ogg, $2',
	'timedmedia-ogg-short-video' => "$1 talaksang pampalabas (''video'') ng Ogg, $2",
	'timedmedia-ogg-short-general' => '$1 talaksang pangmidya ng Ogg, $2',
	'timedmedia-ogg-long-audio' => '$1 talaksang pantunog ng Ogg, haba $2, $3',
	'timedmedia-ogg-long-video' => '$1 talaksan ng palabas ng Ogg, haba $2, $4×$5 mga piksel, $3',
	'timedmedia-ogg-long-multiplexed' => 'magkasanib at nagsasabayang talaksang nadirinig o audio/palabas ng Ogg, $1, haba $2, $4×$5 mga piksel, $3 sa kalahatan',
	'timedmedia-ogg-long-general' => "Talaksang pangmidya ng ''Ogg'', haba $2, $3",
	'timedmedia-ogg-long-error' => "Hindi tanggap na talaksang ''ogg'': $1",
	'timedmedia-no-player-js' => 'Paumahin, ang pantingin-tingin mo ay maaaring may hindi gumaganang JavaScript o walang anumang tinatangkilik na pampaandar.<br />
Maaari kang <a href="$1">magkargang pababa ng kaputol</a> o <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">magkargang pababa ng isang pampaandar</a> upang mapaandar ang kaputol sa loob ng iyong pantingin-tingin.',
	'timedmedia-more' => 'Marami pa…',
	'timedmedia-dismiss' => 'Isara',
	'timedmedia-download' => 'Ikarga ang talaksan',
	'timedmedia-desc-link' => 'Tungkol sa talaksang ito',
	'timedmedia-oggThumb-version' => 'Nangangailangan ang OggHandler ng bersyong $1 o mas luma ng oggThumb.',
	'timedmedia-oggThumb-failed' => 'Nabigong lumikha ang oggThumb ng munting larawan.',
);

/** Turkish (Türkçe)
 * @author Erkan Yilmaz
 * @author Joseph
 * @author Mach
 * @author Runningfridgesrule
 * @author Srhat
 */
$messages['tr'] = array(
	'timedmedia-desc' => 'Ogg Theora ve Vorbis dosyaları için işleyici, JavaScript oynatıcısı ile',
	'timedmedia-ogg-short-audio' => 'Ogg $1 ses dosyası, $2',
	'timedmedia-ogg-short-video' => 'Ogg $1 film dosyası, $2',
	'timedmedia-ogg-short-general' => 'Ogg $1 medya dosyası, $2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 ses dosyası, süre $2, $3',
	'timedmedia-ogg-long-video' => 'Ogg $1 film dosyası, süre $2, $4×$5 piksel, $3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg çok düzeyli ses/film dosyası, $1, süre $2, $4×$5 piksel, $3 genelde',
	'timedmedia-ogg-long-general' => 'Ogg medya dosyası, süre $2, $3',
	'timedmedia-ogg-long-error' => 'Geçersiz ogg dosyası: $1',
	'timedmedia-more' => 'Daha...',
	'timedmedia-dismiss' => 'Kapat',
	'timedmedia-download' => 'Dosya indir',
	'timedmedia-desc-link' => 'Bu dosya hakkında',
);

/** Tsonga (Xitsonga)
 * @author Thuvack
 */
$messages['ts'] = array(
	'timedmedia-more' => 'Swinwana…',
	'timedmedia-dismiss' => 'Pfala',
);

/** Tatar (Cyrillic) (Татарча/Tatarça (Cyrillic))
 * @author Ильнар
 */
$messages['tt-cyrl'] = array(
	'timedmedia-more' => 'Тулырак...',
	'timedmedia-dismiss' => 'Ябу',
	'timedmedia-download' => 'Файлны алу',
	'timedmedia-desc-link' => 'Файл турында мәгълүмат',
	'timedmedia-oggThumb-version' => 'OggHandler $1 юрамасыннан да югарырак oggThumb тәэминатын сорый.',
	'timedmedia-oggThumb-failed' => 'oggThumb нигезендә миниатюраны ясап булмады.',
);

/** Ukrainian (Українська)
 * @author AS
 * @author Ahonc
 * @author NickK
 * @author Prima klasy4na
 */
$messages['uk'] = array(
	'timedmedia-desc' => 'Оброблювач файлів Ogg Theora і Vorbis з використанням JavaScript-програвача',
	'timedmedia-ogg-short-audio' => 'Звуковий файл Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Відео-файл Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Файл Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'звуковий файл Ogg $1, довжина $2, $3',
	'timedmedia-ogg-long-video' => 'відео-файл Ogg $1, довжина $2, $4×$5 пікселів, $3',
	'timedmedia-ogg-long-multiplexed' => 'мультиплексний аудіо/відео-файл ogg, $1, довжина $2, $4×$5 пікселів, $3 усього',
	'timedmedia-ogg-long-general' => 'медіа-файл Ogg, довжина $2, $3',
	'timedmedia-ogg-long-error' => 'Неправильний Ogg-файл: $1',
	'timedmedia-more' => 'Більше…',
	'timedmedia-dismiss' => 'Закрити',
	'timedmedia-download' => 'Завантажити файл',
	'timedmedia-desc-link' => 'Інформація про цей файл',
);

/** Vèneto (Vèneto)
 * @author Candalua
 */
$messages['vec'] = array(
	'timedmedia-desc' => 'Gestor par i file Ogg Theora e Vorbis, con riprodutor JavaScript',
	'timedmedia-ogg-short-audio' => 'File audio Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'File video Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'File multimedial Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'File audio Ogg $1, durata $2, $3',
	'timedmedia-ogg-long-video' => 'File video Ogg $1, durata $2, dimensioni $4×$5 pixel, $3',
	'timedmedia-ogg-long-multiplexed' => 'File audio/video multiplexed Ogg $1, durata $2, dimensioni $4×$5 pixel, conplessivamente $3',
	'timedmedia-ogg-long-general' => 'File multimedial Ogg, durata $2, $3',
	'timedmedia-ogg-long-error' => 'File ogg mìa valido: $1',
	'timedmedia-more' => 'Altro...',
	'timedmedia-dismiss' => 'Sara',
	'timedmedia-download' => 'Descarga el file',
	'timedmedia-desc-link' => 'Informazion su sto file',
);

/** Veps (Vepsan kel')
 * @author Игорь Бродский
 */
$messages['vep'] = array(
	'timedmedia-more' => 'Enamba...',
	'timedmedia-dismiss' => 'Peitta',
	'timedmedia-download' => 'Jügutoitta fail',
	'timedmedia-desc-link' => 'Informacii neciš failas',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 * @author Vinhtantran
 */
$messages['vi'] = array(
	'timedmedia-desc' => 'Bộ trình bày các tập tin Ogg Theora và Vorbis dùng hộp chơi phương tiện bằng JavaScript',
	'timedmedia-ogg-short-audio' => 'Tập tin âm thanh Ogg $1, $2',
	'timedmedia-ogg-short-video' => 'Tập tin video Ogg $1, $2',
	'timedmedia-ogg-short-general' => 'Tập tin Ogg $1, $2',
	'timedmedia-ogg-long-audio' => 'tập tin âm thanh Ogg $1, dài $2, $3',
	'timedmedia-ogg-long-video' => 'tập tin video Ogg $1, dài $2, $4×$5 điểm ảnh, $3',
	'timedmedia-ogg-long-multiplexed' => 'tập tin Ogg có âm thanh và video ghép kênh, $1, dài $2, $4×$5 điểm ảnh, $3 tất cả',
	'timedmedia-ogg-long-general' => 'tập tin phương tiện Ogg, dài $2, $3',
	'timedmedia-ogg-long-error' => 'Tập tin Ogg có lỗi: $1',
	'timedmedia-more' => 'Thêm nữa…',
	'timedmedia-dismiss' => 'Đóng',
	'timedmedia-download' => 'Tải tập tin xuống',
	'timedmedia-desc-link' => 'Chi tiết của tập tin này',
);

/** Volapük (Volapük)
 * @author Malafaya
 * @author Smeira
 */
$messages['vo'] = array(
	'timedmedia-more' => 'Pluikos...',
	'timedmedia-dismiss' => 'Färmükön',
	'timedmedia-download' => 'Donükön ragivi',
	'timedmedia-desc-link' => 'Tefü ragiv at',
);

/** Walloon (Walon) */
$messages['wa'] = array(
	'timedmedia-dismiss' => 'Clôre',
);

/** Yiddish (ייִדיש)
 * @author פוילישער
 */
$messages['yi'] = array(
	'timedmedia-download' => 'אראָפלאָדן טעקע',
);

/** Cantonese (粵語) */
$messages['yue'] = array(
	'timedmedia-desc' => 'Ogg Theora 同 Vorbis 檔案嘅處理器，加埋 JavaScript 播放器',
	'timedmedia-ogg-short-audio' => 'Ogg $1 聲檔，$2',
	'timedmedia-ogg-short-video' => 'Ogg $1 畫檔，$2',
	'timedmedia-ogg-short-general' => 'Ogg $1 媒檔，$2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 聲檔，長度$2，$3',
	'timedmedia-ogg-long-video' => 'Ogg $1 畫檔，長度$2，$4×$5像素，$3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg 多工聲／畫檔，$1，長度$2，$4×$5像素，總共$3',
	'timedmedia-ogg-long-general' => 'Ogg 媒檔，長度$2，$3',
	'timedmedia-ogg-long-error' => '無效嘅ogg檔: $1',
	'timedmedia-more' => '更多...',
	'timedmedia-dismiss' => '閂',
	'timedmedia-download' => '下載檔案',
	'timedmedia-desc-link' => '關於呢個檔案',
);

/** Simplified Chinese (‪中文(简体)‬)
 * @author Gaoxuewei
 */
$messages['zh-hans'] = array(
	'timedmedia-desc' => 'Ogg Theora 和 Vorbis 文件的处理器，含 JavaScript 播放器',
	'timedmedia-ogg-short-audio' => 'Ogg $1 声音文件，$2',
	'timedmedia-ogg-short-video' => 'Ogg $1 视频文件，$2',
	'timedmedia-ogg-short-general' => 'Ogg $1 媒体文件，$2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 声音文件，长度$2，$3',
	'timedmedia-ogg-long-video' => 'Ogg $1 视频文件，长度$2，$4×$5像素，$3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg 多工声音／视频文件，$1，长度$2，$4×$5像素，共$3',
	'timedmedia-ogg-long-general' => 'Ogg 媒体文件，长度$2，$3',
	'timedmedia-ogg-long-error' => '无效的ogg文件: $1',
	'timedmedia-more' => '更多...',
	'timedmedia-dismiss' => '关闭',
	'timedmedia-download' => '下载文件',
	'timedmedia-desc-link' => '关于这个文件',
);

/** Traditional Chinese (‪中文(繁體)‬)
 * @author Gaoxuewei
 * @author Mark85296341
 */
$messages['zh-hant'] = array(
	'timedmedia-desc' => 'Ogg Theora 和 Vorbis 檔案的處理器，含 JavaScript 播放器',
	'timedmedia-ogg-short-audio' => 'Ogg $1 聲音檔案，$2',
	'timedmedia-ogg-short-video' => 'Ogg $1 影片檔案，$2',
	'timedmedia-ogg-short-general' => 'Ogg $1 媒體檔案，$2',
	'timedmedia-ogg-long-audio' => 'Ogg $1 聲音檔案，長度 $2，$3',
	'timedmedia-ogg-long-video' => 'Ogg $1 影片檔案，長度 $2，$4 × $5像素，$3',
	'timedmedia-ogg-long-multiplexed' => 'Ogg 多工聲音／影片檔案，$1，長度 $2，$4 × $5 像素，共 $3',
	'timedmedia-ogg-long-general' => 'Ogg 媒體檔案，長度 $2，$3',
	'timedmedia-ogg-long-error' => '無效的 ogg 檔案：$1',
	'timedmedia-more' => '更多...',
	'timedmedia-dismiss' => '關閉',
	'timedmedia-download' => '下載檔案',
	'timedmedia-desc-link' => '關於這個檔案',
);

