<?php
/**
 * Internationalisation file for extension OggPlayer.
 *
 * @addtogroup Extensions
 */

$messages = array();

$messages['en'] = array(
	'timedmedia-desc'             => 'Handler for timed media (video, audio, timedText) with transcoding to Ogg Theora/Vorbis',
	'timedmedia-short-audio'      => 'Ogg $1 sound file, $2',
	'timedmedia-short-video'      => 'Ogg $1 video file, $2',
	'timedmedia-short-general'    => 'Ogg $1 media file, $2',
	'timedmedia-long-audio'       => '(Ogg $1 sound file, length $2, $3)',
	'timedmedia-long-video'       => '(Ogg $1 video file, length $2, $4×$5 pixels, $3)',
	'timedmedia-long-multiplexed' => '(Ogg multiplexed audio/video file, $1, length $2, $4×$5 pixels, $3 overall)',
	'timedmedia-long-general'     => '(Ogg media file, length $2, $3)',
	'timedmedia-long-error'       => '(Invalid Ogg file: $1)',
	'timedmedia-no-player-js' 	  => 'Sorry, your browser either has JavaScript disabled or does not have any supported player.<br />
You can <a href="$1">download the clip</a> or <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">download a player</a> to play the clip in your browser.',

	'timedmedia-more'             => 'More…',
	'timedmedia-dismiss'          => 'Close',
	'timedmedia-download'         => 'Download file',
	'timedmedia-desc-link'        => 'About this file',
	'timedmedia-oggThumb-version' => 'OggHandler requires oggThumb version $1 or later.',
	'timedmedia-oggThumb-failed'  => 'oggThumb failed to create the thumbnail.',
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
 * @author Siebrand
 */
$messages['qqq'] = array(
	'timedmedia-desc' => '{{desc}}',
	'timedmedia-short-general' => 'File details for generic (non-audio, non-video) Ogg files, short version.
Parameters are:
* $1 file type, e.g. Vorbis, Speex
* $2 ?',
	'timedmedia-long-audio' => 'File details for Ogg files, shown after the filename in the image description page.
Parameters are:
* $1 file codec, f.e. Vorbis, Speex
* $2 file duration, f.e. 1m34s
* $3 file sampling rate, f.e. 97kbps',
	'timedmedia-play' => '{{Identical|Play}}',
	'timedmedia-player-videoElement' => 'Message used in JavaScript.',
	'timedmedia-player-vlc-mozilla' => '{{optional}}',
	'timedmedia-player-quicktime-mozilla' => '{{optional}}',
	'timedmedia-player-totem' => '{{optional}}',
	'timedmedia-player-kmplayer' => '{{optional}}',
	'timedmedia-player-kaffeine' => '{{optional}}',
	'timedmedia-more' => '{{Identical|More...}}',
	'timedmedia-dismiss' => '{{Identical|Close}}',
	'timedmedia-download' => '{{Identical|Download}}',
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
	'timedmedia-short-audio' => 'Ogg $1 klanklêer, $2',
	'timedmedia-short-video' => 'Ogg $1 video lêer, $2',
	'timedmedia-short-general' => 'Ogg $1 medialêer, $2',
	'timedmedia-long-audio' => '(Ogg $1 klanklêer, lengte $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 videolêer, lengte $2, $4×$5 pixels, $3)',
	'timedmedia-long-general' => '(Ogg medialêer, lengte $2, $3)',
	'timedmedia-long-error' => '(Ongeldige timedmedia-lêer: $1)',
	'timedmedia-play' => 'Speel',
	'timedmedia-pause' => 'Wag',
	'timedmedia-stop' => 'Stop',
	'timedmedia-play-video' => 'Speel video',
	'timedmedia-play-sound' => 'Speel geluid',
	'timedmedia-player-videoElement' => 'Standaardondersteuning in webblaaier',
	'timedmedia-player-oggPlugin' => 'Webblaaier-plugin',
	'timedmedia-player-soundthumb' => 'Geen mediaspeler',
	'timedmedia-player-selected' => '(geselekteer)',
	'timedmedia-use-player' => 'Gebruik speler:',
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
	'timedmedia-short-audio' => 'Ogg tingull $1 fotografi, $2',
	'timedmedia-short-video' => 'video file Ogg $1, $2',
	'timedmedia-short-general' => 'Ogg $1 media file, $2',
	'timedmedia-long-audio' => '(ZQM file $1 shëndoshë, gjatë $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 video file, gjatë $2, $4 × $5 pixels, $3)',
	'timedmedia-long-multiplexed' => '(ZQM multiplexed audio / video file, $1, gjatë $2, $4 × $5 pixels, $3 e përgjithshme)',
	'timedmedia-long-general' => '(Ogg media file, gjatë $2, $3)',
	'timedmedia-long-error' => '(E pavlefshme Ogg file: $1)',
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
	'timedmedia-desc' => 'Manullador ta archibos Ogg Theora and Vorbis, con un reproductor JavaScript',
	'timedmedia-short-audio' => 'Archibo de son ogg $1, $2',
	'timedmedia-short-video' => 'Archibo de bidio ogg $1, $2',
	'timedmedia-short-general' => 'Archibo multimedia ogg $1, $2',
	'timedmedia-long-audio' => '(Archibo de son ogg $1, durada $2, $3)',
	'timedmedia-long-video' => '(Archibo de bidio ogg $1, durada $2, $4×$5 píxels, $3)',
	'timedmedia-long-multiplexed' => '(archibo ogg multiplexato audio/bidio, $1, durada $2, $4×$5 píxels, $3 total)',
	'timedmedia-long-general' => '(archibo ogg multimedia durada $2, $3)',
	'timedmedia-long-error' => '(Archibo ogg no conforme: $1)',
	'timedmedia-play' => 'Reproduzir',
	'timedmedia-pause' => 'Pausa',
	'timedmedia-stop' => 'Aturar',
	'timedmedia-play-video' => 'Reproduzir bidio',
	'timedmedia-play-sound' => 'Reproduzir son',
	'timedmedia-no-player' => 'No puedo trobar garra software reproductor suportato.
Abría d\'<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">escargar un reproductor</a>.',
	'timedmedia-no-xiphqt' => 'No puedo trobar o component XiphQT ta QuickTime.
QuickTime no puede reproduzir archibos ogg sin este component.
Puede <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">escargar XiphQT</a> u trigar un atro reproductor.',
	'timedmedia-player-videoElement' => "Soporte natibo d'o nabegador",
	'timedmedia-player-oggPlugin' => "Plugin d'o nabegador",
	'timedmedia-player-thumbnail' => 'Nomás imachen fixa',
	'timedmedia-player-soundthumb' => 'Garra reproductor',
	'timedmedia-player-selected' => '(trigato)',
	'timedmedia-use-player' => 'Fer serbir o reprodutor:',
	'timedmedia-more' => 'Más…',
	'timedmedia-dismiss' => 'Zarrar',
	'timedmedia-download' => 'Escargar archibo',
	'timedmedia-desc-link' => 'Informazión sobre este archibo',
);

/** Arabic (العربية)
 * @author Alnokta
 * @author Meno25
 * @author OsamaK
 */
$messages['ar'] = array(
	'timedmedia-desc' => 'متحكم لملفات Ogg Theora وVorbis، مع لاعب جافاسكريت',
	'timedmedia-short-audio' => 'Ogg $1 ملف صوت، $2',
	'timedmedia-short-video' => 'Ogg $1 ملف فيديو، $2',
	'timedmedia-short-general' => 'Ogg $1 ملف ميديا، $2',
	'timedmedia-long-audio' => '(Ogg $1 ملف صوت، الطول $2، $3)',
	'timedmedia-long-video' => '(Ogg $1 ملف فيديو، الطول $2، $4×$5 بكسل، $3)',
	'timedmedia-long-multiplexed' => '(ملف Ogg مالتي بليكسد أوديو/فيديو، $1، الطول $2، $4×$5 بكسل، $3 إجمالي)',
	'timedmedia-long-general' => '(ملف ميديا Ogg، الطول $2، $3)',
	'timedmedia-long-error' => '(ملف Ogg غير صحيح: $1)',
	'timedmedia-play' => 'عرض',
	'timedmedia-pause' => 'إيقاف مؤقت',
	'timedmedia-stop' => 'إيقاف',
	'timedmedia-play-video' => 'عرض الفيديو',
	'timedmedia-play-sound' => 'عرض الصوت',
	'timedmedia-no-player' => 'معذرة ولكن يبدو أنه لا يوجد لديك برنامج عرض مدعوم. من فضلك ثبت <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">الجافا</a>.',
	'timedmedia-no-xiphqt' => 'لا يبدو أنك تملك مكون XiphQT لكويك تايم.
كويك تايم لا يمكنه عرض ملفات Ogg بدون هذا المكون.
من فضلك <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">حمل XiphQT</a> أو اختر برنامجا آخر.',
	'timedmedia-player-videoElement' => 'دعم متصفح مدمج',
	'timedmedia-player-oggPlugin' => 'إضافة متصفح',
	'timedmedia-player-cortado' => 'كورتادو (جافا)',
	'timedmedia-player-vlc-mozilla' => 'في إل سي',
	'timedmedia-player-vlc-activex' => 'في إل سي (أكتيف إكس)',
	'timedmedia-player-quicktime-mozilla' => 'كويك تايم',
	'timedmedia-player-quicktime-activex' => 'كويك تايم (أكتيف إكس)',
	'timedmedia-player-totem' => 'توتيم',
	'timedmedia-player-kmplayer' => 'كيه إم بلاير',
	'timedmedia-player-kaffeine' => 'كافيين',
	'timedmedia-player-mplayerplug-in' => 'إضافة إم بلاير',
	'timedmedia-player-thumbnail' => 'مازال صورة فقط',
	'timedmedia-player-soundthumb' => 'لا برنامج',
	'timedmedia-player-selected' => '(مختار)',
	'timedmedia-use-player' => 'استخدم البرنامج:',
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
	'timedmedia-short-audio' => 'Ogg $1 ملف صوت، $2',
	'timedmedia-short-video' => 'Ogg $1 ملف فيديو, $2',
	'timedmedia-short-general' => 'Ogg $1 ملف ميديا، $2',
	'timedmedia-long-audio' => '(Ogg $1 ملف صوت، الطول $2، $3)',
	'timedmedia-long-video' => '(Ogg $1 ملف فيديو، الطول $2، $4×$5 بكسل، $3)',
	'timedmedia-long-multiplexed' => '(ملف Ogg مالتى بليكسد أوديو/فيديو، $1، الطول $2، $4×$5 بكسل، $3 إجمالي)',
	'timedmedia-long-general' => '(ملف ميديا Ogg، الطول $2، $3)',
	'timedmedia-long-error' => '(ملف ogg مش صحيح: $1)',
	'timedmedia-play' => 'شغل',
	'timedmedia-pause' => ' توقيف مؤقت',
	'timedmedia-stop' => 'توقيف',
	'timedmedia-play-video' => 'شغل الفيديو',
	'timedmedia-play-sound' => 'شغل الصوت',
	'timedmedia-no-player' => 'متاسفين الظاهر أنه ماعندكش برنامج عرض مدعوم.
لو سمحت تنزل < a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">الجافا</a>.',
	'timedmedia-no-xiphqt' => 'الظاهر انه ماعندكش مكون الـ XiphQT لكويك تايم.
كويك تايم مش ممكن يعرض ملفات Ogg  من غير المكون دا.
لو سمحت <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">تنزل XiphQT</a> أو تختار برنامج تانى.',
	'timedmedia-player-videoElement' => 'دعم البراوزر الاصلي',
	'timedmedia-player-oggPlugin' => 'اضافة براوزر',
	'timedmedia-player-cortado' => 'كورتادو (جافا)',
	'timedmedia-player-vlc-mozilla' => 'فى إل سي',
	'timedmedia-player-vlc-activex' => 'فى إل سى (أكتيف إكس)',
	'timedmedia-player-quicktime-mozilla' => 'كويك تايم',
	'timedmedia-player-quicktime-activex' => 'كويك تايم (أكتيف إكس)',
	'timedmedia-player-totem' => 'توتيم',
	'timedmedia-player-kmplayer' => 'كيه إم بلاير',
	'timedmedia-player-kaffeine' => 'كافيين',
	'timedmedia-player-mplayerplug-in' => 'إضافة إم بلاير',
	'timedmedia-player-thumbnail' => 'صورة ثابتة بس',
	'timedmedia-player-soundthumb' => 'ما فيش برنامج',
	'timedmedia-player-selected' => '(مختار)',
	'timedmedia-use-player' => 'استخدم البرنامج:',
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
	'timedmedia-short-audio' => 'Archivu de soníu ogg $1, $2',
	'timedmedia-short-video' => 'Archivu de videu ogg $1, $2',
	'timedmedia-short-general' => 'Archivu multimedia ogg $1, $2',
	'timedmedia-long-audio' => '(Archivu de soníu ogg $1, llonxitú $2, $3)',
	'timedmedia-long-video' => '(Archivu de videu ogg $1, llonxitú $2, $4×$5 píxeles, $3)',
	'timedmedia-long-multiplexed' => "(Archivu d'audiu/videu ogg multiplexáu, $1, llonxitú $2, $4×$5 píxeles, $3)",
	'timedmedia-long-general' => '(Archivu multimedia ogg, llonxitú $2, $3)',
	'timedmedia-long-error' => '(Archivu ogg non válidu: $1)',
	'timedmedia-play' => 'Reproducir',
	'timedmedia-pause' => 'Pausar',
	'timedmedia-stop' => 'Aparar',
	'timedmedia-play-video' => 'Reproducir videu',
	'timedmedia-play-sound' => 'Reproducir soníu',
	'timedmedia-no-player' => 'Sentímoslo, el to sistema nun paez tener nengún de los reproductores soportaos. Por favor <a
href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">descarga un reproductor</a>.',
	'timedmedia-no-xiphqt' => 'Paez que nun tienes el componente XiphQT pa QuickTime. QuickTime nun pue reproducr archivos ogg ensin esti componente. Por favor <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">descarga XiphQT</a> o escueyi otru reproductor.',
	'timedmedia-player-videoElement' => 'Soporte nativu del navegador',
	'timedmedia-player-oggPlugin' => 'Plugin del navegador',
	'timedmedia-player-thumbnail' => 'Namái imaxe en pausa',
	'timedmedia-player-soundthumb' => 'Nun hai reproductor',
	'timedmedia-player-selected' => '(seleicionáu)',
	'timedmedia-use-player' => 'Utilizar el reproductor:',
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

/** Samogitian (Žemaitėška)
 * @author Hugo.arg
 */
$messages['bat-smg'] = array(
	'timedmedia-play' => 'Gruotė',
	'timedmedia-pause' => 'Pauzė',
	'timedmedia-stop' => 'Sostabdītė',
	'timedmedia-play-video' => 'Gruotė video',
	'timedmedia-play-sound' => 'Gruotė garsa',
	'timedmedia-download' => 'Atsėsiōstė faila',
);

/** Southern Balochi (بلوچی مکرانی)
 * @author Mostafadaneshvar
 */
$messages['bcc'] = array(
	'timedmedia-desc' => 'دسگیره په فایلان Ogg Theora و Vorbis, گون پخش کنوک جاوا اسکرسیپت',
	'timedmedia-short-audio' => 'فایل صوتی Ogg $1، $2',
	'timedmedia-short-video' => 'فایل تصویری Ogg $1، $2',
	'timedmedia-short-general' => 'فایل مدیا Ogg $1، $2',
	'timedmedia-long-audio' => '(اوجی جی  $1 فایل صوتی, طول $2, $3)',
	'timedmedia-long-video' => '(اوجی جی $1 فایل ویدیو, طول $2, $4×$5 پیکسل, $3)',
	'timedmedia-long-multiplexed' => '(اوجی جی چند دابی فایل صوت/تصویر, $1, طول $2, $4×$5 پیکسل, $3 کل)',
	'timedmedia-long-general' => '(اوجی جی فایل مدیا, طول $2, $3)',
	'timedmedia-long-error' => '(نامعتبرین فایل اوجی جی: $1)',
	'timedmedia-play' => 'پخش',
	'timedmedia-pause' => 'توقف',
	'timedmedia-stop' => 'بند',
	'timedmedia-play-video' => 'پخش ویدیو',
	'timedmedia-play-sound' => 'پخش توار',
	'timedmedia-no-player' => 'شرمنده،شمی سیستم جاه کیت که هچ برنامه حمایتی پخش کنوک نیست.
لطفا <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download"> یک پخش کنوکی ای گیزیت</a>.',
	'timedmedia-no-xiphqt' => 'چوش جاه کیت که شما را جز XiphQTپه کویک تایم نیست.
کویک تایم بی ای جز نه تونیت فایلان اوجی جی بوانیت.
لطف <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ایرگیزیت XiphQT</a> یا دگه وانوکی انتخاب کنیت.',
	'timedmedia-player-videoElement' => '<video> جزء',
	'timedmedia-player-oggPlugin' => ' پلاگین اوجی جی',
	'timedmedia-player-cortado' => 'کارتادو(جاوا)',
	'timedmedia-player-vlc-mozilla' => 'وی ال سی',
	'timedmedia-player-vlc-activex' => 'VLC (ActiveX)وی ال سی',
	'timedmedia-player-quicktime-mozilla' => 'کویک تایم',
	'timedmedia-player-quicktime-activex' => 'QuickTime (ActiveX) کویک تایم',
	'timedmedia-player-thumbnail' => 'هنگت فقط عکس',
	'timedmedia-player-soundthumb' => 'هچ پخش کنوک',
	'timedmedia-player-selected' => '(انتخابی)',
	'timedmedia-use-player' => 'استفاده کن پخش کنوک',
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

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 * @author Red Winged Duck
 */
$messages['be-tarask'] = array(
	'timedmedia-desc' => 'Апрацоўшчык файлаў Ogg Theora і Vorbis з прайгравальнікам JavaScript',
	'timedmedia-short-audio' => 'Аўдыё-файл Ogg $1, $2',
	'timedmedia-short-video' => 'Відэа-файл у фармаце Ogg $1, $2',
	'timedmedia-short-general' => 'Мэдыя-файл Ogg $1, $2',
	'timedmedia-long-audio' => '(аўдыё-файл Ogg $1, даўжыня $2, $3)',
	'timedmedia-long-video' => '(відэа-файл Ogg $1, даўжыня $2, $4×$5 піксэляў, $3)',
	'timedmedia-long-multiplexed' => '(мультыплексны аўдыё/відэа-файл Ogg, $1, даўжыня $2, $4×$5 піксэляў, усяго $3)',
	'timedmedia-long-general' => '(мэдыя-файл Ogg, даўжыня $2, $3)',
	'timedmedia-long-error' => '(Няслушны файл у фармаце Ogg: $1)',
	'timedmedia-no-player-js' => 'Прабачце, але ў Вашым браўзэры адключаны JavaScript альбо няма неабходнага прайгравальніка.<br />
Вы можаце <a href="$1">загрузіць кліп</a> ці <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">загрузіць прайгравальнік</a> для прайграваньня кліпу ў Вашым браўзэры.',
	'timedmedia-more' => 'Болей…',
	'timedmedia-dismiss' => 'Зачыніць',
	'timedmedia-download' => 'Загрузіць файл',
	'timedmedia-desc-link' => 'Інфармацыя пра гэты файл',
	'timedmedia-oggThumb-version' => 'OggHandler патрабуе oggThumb вэрсіі $1 ці больш позьняй.',
	'timedmedia-oggThumb-failed' => 'oggThumb не атрымалася стварыць мініятуру.',
);

/** Bulgarian (Български)
 * @author Borislav
 * @author DCLXVI
 * @author Spiritia
 */
$messages['bg'] = array(
	'timedmedia-desc' => 'Приложение за файлове тип Ogg Theora и Vorbis, с плейър на JavaScript',
	'timedmedia-short-audio' => 'Ogg $1 звуков файл, $2',
	'timedmedia-short-video' => 'Ogg $1 видео файл, $2',
	'timedmedia-long-audio' => '(Ogg $1 звуков файл, продължителност $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 видео файл, продължителност $2, $4×$5 пиксела, $3)',
	'timedmedia-long-general' => '(Мултимедиен файл в ogg формат с дължина $2, $3)',
	'timedmedia-long-error' => '(Невалиден ogg файл: $1)',
	'timedmedia-play' => 'Пускане',
	'timedmedia-pause' => 'Пауза',
	'timedmedia-stop' => 'Спиране',
	'timedmedia-play-video' => 'Пускане на видео',
	'timedmedia-play-sound' => 'Пускане на звук',
	'timedmedia-no-player' => 'Съжаляваме, но на вашия компютър изглежда няма някой от поддържаните плейъри.
Моля <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">изтеглете си плейър</a>.',
	'timedmedia-no-xiphqt' => 'Изглежда нямате инсталиран компонента XiphQT за QuickTime.
Без този компонент, QuickTime не може да пуска файлове във формат Ogg.
Моля, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">свалете си XiphQT</a> или изберете друго приложение.',
	'timedmedia-player-videoElement' => 'Локална поддръжка от браузъра',
	'timedmedia-player-oggPlugin' => 'Плъгин към браузъра',
	'timedmedia-player-thumbnail' => 'Само неподвижни изображения',
	'timedmedia-player-soundthumb' => 'Няма плеър',
	'timedmedia-player-selected' => '(избран)',
	'timedmedia-use-player' => 'Ползване на плеър:',
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
	'timedmedia-short-audio' => 'অগ $1 সাউন্ড ফাইল, $2',
	'timedmedia-short-video' => 'অগ $1 ভিডিও ফাইল, $2',
	'timedmedia-short-general' => 'অগ $1 মিডিয়া ফাইল, $2',
	'timedmedia-long-audio' => '(অগ $1 সাউন্ড ফাইল, দৈর্ঘ্য $2, $3)',
	'timedmedia-long-video' => '(অগ $1 ভিডিও ফাইল, দৈর্ঘ্য $2, $4×$5 পিক্সেল, $3)',
	'timedmedia-long-multiplexed' => '(অগ মাল্টিপ্লেক্সকৃত অডিও/ভিডিও ফাইল, $1, দৈর্ঘ্য $2, $4×$5 পিক্সেল, $3 সামগ্রিক)',
	'timedmedia-long-general' => '(অগ মিডিয়া ফাইল, দৈর্ঘ্য $2, $3)',
	'timedmedia-long-error' => '(অবৈধ অগ ফাইল: $1)',
	'timedmedia-play' => 'চালানো হোক',
	'timedmedia-pause' => 'বিরতি',
	'timedmedia-stop' => 'বন্ধ',
	'timedmedia-play-video' => 'ভিডিও চালানো হোক',
	'timedmedia-play-sound' => 'অডিও চালানো হোক',
	'timedmedia-no-player' => 'দুঃখিত, আপনার কম্পিউটারে ফাইলটি চালনার জন্য কোন সফটওয়্যার নেই। অনুগ্রহ করে <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">চালনাকারী সফটওয়্যার ডাউনলোড করুন</a>।',
	'timedmedia-no-xiphqt' => 'আপনার কুইকটাইম সফটওয়্যারটিতে XiphQT উপাদানটি নেই। এই উপাদানটি ছাড়া কুইকটাইম অগ ফাইল চালাতে পারবে না। অনুগ্রহ করে <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT ডাউনলোড করুন</a> অথবা অন্য একটি চালনাকারী সফটওয়্যার ব্যবহার করুন।',
	'timedmedia-player-videoElement' => 'স্থানীয় ব্রাউজার সাপোর্ট',
	'timedmedia-player-oggPlugin' => 'ব্রাউজার প্লাগ-ইন',
	'timedmedia-player-thumbnail' => 'শুধুমাত্র স্থির চিত্র',
	'timedmedia-player-soundthumb' => 'কোন চালনাকারী সফটওয়্যার নেই',
	'timedmedia-player-selected' => '(নির্বাচিত)',
	'timedmedia-use-player' => 'এই চালনাকারী সফটওয়্যার ব্যবহার করুন:',
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
	'timedmedia-short-audio' => 'Restr son Ogg $1, $2',
	'timedmedia-short-video' => 'Restr video Ogg $1, $2',
	'timedmedia-short-general' => 'Restr media Ogg $1, $2',
	'timedmedia-long-audio' => '(Restr son Ogg $1, pad $2, $3)',
	'timedmedia-long-video' => '(Restr video Ogg $1, pad $2, $4×$5 piksel, $3)',
	'timedmedia-long-multiplexed' => '(Restr Ogg klevet/video liesplezhet $1, pad $2, $4×$5 piksel, $3 hollad)',
	'timedmedia-long-general' => '(Restr media Ogg, pad $2, $3)',
	'timedmedia-long-error' => '(Restr ogg direizh : $1)',
	'timedmedia-no-player-js' => 'Ho tigarez, pe eo diweredekaet JavaScript war ho merdeer pen n\'eo ket skoret lenner ebet gantañ.<br />
<a href="$1">Pellgargañ ar c\'hlip</a> a c\'hallit pe <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">pellgargañ ul lenner</a> da lenn ar c\'hlip gant ho merdeer.',
	'timedmedia-more' => "Muioc'h...",
	'timedmedia-dismiss' => 'Serriñ',
	'timedmedia-download' => 'Pellgargañ ar restr',
	'timedmedia-desc-link' => 'Diwar-benn ar restr-mañ',
	'timedmedia-oggThumb-version' => "Rekis eo stumm $1 oggThumb, pe nevesoc'h, evit implijout OggHandler.",
	'timedmedia-oggThumb-failed' => "N'eo ket deuet a-benn oggThumb da grouiñ ar munud.",
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'timedmedia-desc' => 'Upravljač za Ogg Theora i Vorbis datotekem sa JavaScript preglednikom',
	'timedmedia-short-audio' => 'Ogg $1 zvučna datoteka, $2',
	'timedmedia-short-video' => 'Ogg $1 video datoteka, $2',
	'timedmedia-short-general' => 'Ogg $1 medijalna datoteka, $2',
	'timedmedia-long-audio' => '(Ogg $1 zvučna datoteka, dužina $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 video datoteka, dužina $2, $4×$5 piksela, $3)',
	'timedmedia-long-multiplexed' => '(Ogg multipleksna zvučna/video datoteka, $1, dužina $2, $4×$5 piksela, $3 sveukupno)',
	'timedmedia-long-general' => '(Ogg medijalna datoteka, dužina $2, $3)',
	'timedmedia-long-error' => '(Nevaljana ogg datoteka: $1)',
	'timedmedia-play' => 'Pokreni',
	'timedmedia-pause' => 'Pauza',
	'timedmedia-stop' => 'Zaustavi',
	'timedmedia-play-video' => 'Pokreni video',
	'timedmedia-play-sound' => 'Sviraj zvuk',
	'timedmedia-no-player' => 'Žao nam je, Vaš sistem izgleda da nema nikakvog podržanog softvera za pregled.
Molimo Vas <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">da skinete preglednik</a>.',
	'timedmedia-no-xiphqt' => 'Izgleda da nemate XiphQT komponentu za program QuickTime.
QuickTime ne može reproducirati Ogg datoteke bez ove komponente.
Molimo Vas da <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">skinete XiphQT</a> ili da odaberete drugi preglednik.',
	'timedmedia-player-videoElement' => 'Prirodna podrška preglednika',
	'timedmedia-player-oggPlugin' => 'Dodatak pregledniku',
	'timedmedia-player-thumbnail' => 'Samo mirne slike',
	'timedmedia-player-soundthumb' => 'Nema preglednika',
	'timedmedia-player-selected' => '(odabrano)',
	'timedmedia-use-player' => 'Koristi svirač:',
	'timedmedia-more' => 'Više...',
	'timedmedia-dismiss' => 'Zatvori',
	'timedmedia-download' => 'Učitaj datoteku',
	'timedmedia-desc-link' => 'O ovoj datoteci',
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
	'timedmedia-short-audio' => "Fitxer OGG d'àudio $1, $2",
	'timedmedia-short-video' => 'Fitxer OGG de vídeo $1, $2',
	'timedmedia-short-general' => 'Fitxer multimèdia OGG $1, $2',
	'timedmedia-long-audio' => '(Ogg $1 fitxer de so, llargada $2, $3)',
	'timedmedia-long-video' => '(Fitxer OGG de vídeo $1, llargada $2, $4×$5 píxels, $3)',
	'timedmedia-long-multiplexed' => '(Arxiu àudio/vídeo multiplex, $1, llargada $2, $4×$5 píxels, $3 de mitjana)',
	'timedmedia-long-general' => '(Fitxer multimèdia OGG, llargada $2, $3)',
	'timedmedia-long-error' => '(Fitxer OGG invàlid: $1)',
	'timedmedia-play' => 'Reprodueix',
	'timedmedia-pause' => 'Pausa',
	'timedmedia-stop' => 'Atura',
	'timedmedia-play-video' => 'Reprodueix vídeo',
	'timedmedia-play-sound' => 'Reprodueix so',
	'timedmedia-no-player' => 'No teniu instaŀlat cap reproductor acceptat. Podeu <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">descarregar-ne</a> un.',
	'timedmedia-no-xiphqt' => 'No disposeu del component XiphQT al vostre QuickTime. Aquest component és imprescindible per a que el QuickTime pugui reproduir fitxers OGG. Podeu <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">descarregar-lo</a> o escollir un altre reproductor.',
	'timedmedia-player-videoElement' => 'Suport natiu del navegador',
	'timedmedia-player-oggPlugin' => 'Connector del navegador',
	'timedmedia-player-thumbnail' => 'Només un fotograma',
	'timedmedia-player-soundthumb' => 'Cap reproductor',
	'timedmedia-player-selected' => '(seleccionat)',
	'timedmedia-use-player' => 'Usa el reproductor:',
	'timedmedia-more' => 'Més...',
	'timedmedia-dismiss' => 'Tanca',
	'timedmedia-download' => 'Descarrega el fitxer',
	'timedmedia-desc-link' => 'Informació del fitxer',
);

/** Czech (Česky)
 * @author Li-sung
 * @author Matěj Grabovský
 * @author Mormegil
 */
$messages['cs'] = array(
	'timedmedia-desc' => 'Obsluha souborů Ogg Theora a Vorbis s JavaScriptovým přehrávačem',
	'timedmedia-short-audio' => 'Zvukový soubor ogg $1, $2',
	'timedmedia-short-video' => 'Videosoubor ogg $1, $2',
	'timedmedia-short-general' => 'Soubor média ogg $1, $2',
	'timedmedia-long-audio' => '(Zvukový soubor ogg $1, délka $2, $3)',
	'timedmedia-long-video' => '(Videosoubor $1, délka $2, $4×$5 pixelů, $3)',
	'timedmedia-long-multiplexed' => '(Audio/video soubor ogg, $1, délka $2, $4×$5 pixelů, $3)',
	'timedmedia-long-general' => '(Soubor média ogg, délka $2, $3)',
	'timedmedia-long-error' => '(Chybný soubor ogg: $1)',
	'timedmedia-play' => 'Přehrát',
	'timedmedia-pause' => 'Pozastavit',
	'timedmedia-stop' => 'Zastavit',
	'timedmedia-play-video' => 'Přehrát video',
	'timedmedia-play-sound' => 'Přehrát zvuk',
	'timedmedia-no-player' => 'Váš systém zřejmě neobsahuje žádný podporovaný přehrávač. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Váš systém zřejmě neobsahuje žádný podporovaný přehrávač. </a>.',
	'timedmedia-no-xiphqt' => 'Nemáte rozšíření XiphQT pro QuickTime. QuickTime nemůže přehrávat soubory ogg bez tohoto rozšíření. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Stáhněte XiphQT</a> nebo vyberte jiný přehrávač.',
	'timedmedia-player-videoElement' => 'Vestavěná podpora v prohlížeči',
	'timedmedia-player-oggPlugin' => 'Zásuvný modul do prohlížeče',
	'timedmedia-player-thumbnail' => 'Pouze snímek náhledu',
	'timedmedia-player-soundthumb' => 'Žádný přehrávač',
	'timedmedia-player-selected' => '(zvoleno)',
	'timedmedia-use-player' => 'Vyberte přehrávač:',
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
	'timedmedia-short-audio' => 'Ogg $1 lydfil, $2',
	'timedmedia-short-video' => 'Ogg $1 videofil, $2',
	'timedmedia-short-general' => 'Ogg $1 mediafil, $2',
	'timedmedia-long-audio' => '(Ogg $1 lydfil, længde $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 videofil, længde $2, $4×$5 pixel, $3)',
	'timedmedia-long-multiplexed' => '(Sammensat timedmedia-lyd- og -videofil, $1, længde $2, $4×$5 pixel, $3 samlet)',
	'timedmedia-long-general' => '(Ogg mediafil, længde $2, $3)',
	'timedmedia-long-error' => '(Ugyldig timedmedia-fil: $1)',
	'timedmedia-play' => 'Afspil',
	'timedmedia-pause' => 'Pause',
	'timedmedia-stop' => 'Stop',
	'timedmedia-play-video' => 'Afspil video',
	'timedmedia-play-sound' => 'Afspil lyd',
	'timedmedia-no-player' => 'Desværre ser det ud til at dit system har nogen understøttede medieafspillere.
<a href="http://mediawiki.org/wiki/Extension:OggHandler/Client_download">Download venligst en afspiller</a>.',
	'timedmedia-no-xiphqt' => 'Det ser ud til at du ikke har XiphQT-komponenten til QuickTime.
QuickTime kan ikke afspille timedmedia-file uden denne komponent.
<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Download venligst XiphQT</a> eller vælg en anden afspiller.',
	'timedmedia-player-videoElement' => 'Indbygget browserunderstøttelse',
	'timedmedia-player-oggPlugin' => 'Browsertilføjelse',
	'timedmedia-player-thumbnail' => 'Kun stillbilleder',
	'timedmedia-player-soundthumb' => 'Ingen afspiller',
	'timedmedia-player-selected' => '(valgt)',
	'timedmedia-use-player' => 'Brug afspiller:',
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
	'timedmedia-desc' => 'Steuerungsprogramm für Ogg Theora- und Vorbis-Dateien, inklusive einer JavaScript-Abspielsoftware',
	'timedmedia-short-audio' => 'Ogg-$1-Audiodatei, $2',
	'timedmedia-short-video' => 'Ogg-$1-Videodatei, $2',
	'timedmedia-short-general' => 'Ogg-$1-Mediadatei, $2',
	'timedmedia-long-audio' => '(Ogg-$1-Audiodatei, Länge: $2, $3)',
	'timedmedia-long-video' => '(Ogg-$1-Videodatei, Länge: $2, $4×$5 Pixel, $3)',
	'timedmedia-long-multiplexed' => '(Ogg-Audio-/Video-Datei, $1, Länge: $2, $4×$5 Pixel, $3)',
	'timedmedia-long-general' => '(Ogg-Mediadatei, Länge: $2, $3)',
	'timedmedia-long-error' => '(Ungültige Ogg-Datei: $1)',
	'timedmedia-no-player-js' => 'Entschuldige, aber dein Browser hat entweder JavaScript deaktiviert oder keine unterstützte Abspielsoftware.<br />
Du kannst <a href="$1">den Clip herunterladen</a> oder <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">eine Abspielsoftware herunterladen</a>, um den Clip im Browser abzuspielen.',
	'timedmedia-more' => 'Optionen …',
	'timedmedia-dismiss' => 'Schließen',
	'timedmedia-download' => 'Datei speichern',
	'timedmedia-desc-link' => 'Über diese Datei',
	'timedmedia-oggThumb-version' => 'OggHandler erfordert oggThumb in der Version $1 oder höher.',
	'timedmedia-oggThumb-failed' => 'oggThumb konnte kein Miniaturbild erstellen.',
);

/** German (formal address) (Deutsch (Sie-Form))
 * @author Raimond Spekking
 * @author Umherirrender
 */
$messages['de-formal'] = array(
	'timedmedia-no-player' => 'Ihr System scheint über keine Abspielsoftware zu verfügen. Bitte installieren Sie <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">eine Abspielsoftware</a>.',
	'timedmedia-no-xiphqt' => 'Ihr System scheint nicht über die XiphQT-Komponente für QuickTime zu verfügen. QuickTime kann ohne diese Komponente keine timedmedia-Dateien abspielen.Bitte <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">laden Sie XiphQT</a> oder wählen Sie eine andere Abspielsoftware.',
);

/** Zazaki (Zazaki)
 * @author Aspar
 * @author Xoser
 */
$messages['diq'] = array(
	'timedmedia-desc' => 'Qe dosyayanê Ogg Theora u Vorbisî pê JavaScriptî qulp',
	'timedmedia-short-audio' => 'Ogg $1 dosyaya vengi, $2',
	'timedmedia-short-video' => 'Ogg $1 dosyaya filmi, $2',
	'timedmedia-short-general' => 'Ogg $1 dosyaya medyayi, $2',
	'timedmedia-long-audio' => '(Ogg $1 dosyaya medyayi,  mudde $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 dosyaya filmi, mudde $2, $4×$5 piksel, $3)',
	'timedmedia-long-multiplexed' => '(Ogg dosyaya filmi/vengi yo multiexed, $1, mudde $2, $4×$5 piksel, $3 bıumumi)',
	'timedmedia-long-general' => '(Ogg dosyaya medyayi, mudde $2, $3)',
	'timedmedia-long-error' => '(dosyaya oggi yo nemeqbul: $1)',
	'timedmedia-play' => "bıd' kaykerdış",
	'timedmedia-pause' => 'vındarn',
	'timedmedia-stop' => 'vındarn',
	'timedmedia-play-video' => "video bıd' kaykerdış",
	'timedmedia-play-sound' => "veng bıd' kaykerdış",
	'timedmedia-no-player' => 'ma meluli, wina aseno ke sistemê şıma wayirê softwareyi yo player niyo.
kerem kerê <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">yew player biyare war</a>.',
	'timedmedia-no-xiphqt' => 'qey QuickTimeyi wina aseno ke şıma wayirê parçeyê XiphQTi niyê.
heta ke parçeyê QuickTimeyi çinibi dosyayê Oggyi nêxebıtiyeni.
kerem kerê<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT\'i biyar war</a> ya zi yewna player bıvıcinê.',
	'timedmedia-player-videoElement' => 'destekê cıgêrayoxê mehelliyi',
	'timedmedia-player-oggPlugin' => 'zeylê cıgêrayoxi',
	'timedmedia-player-thumbnail' => 'hema têna resm o.',
	'timedmedia-player-soundthumb' => 'player çino',
	'timedmedia-player-selected' => '(vıciyaye)',
	'timedmedia-use-player' => 'player bışuxuln:',
	'timedmedia-more' => 'hema....',
	'timedmedia-dismiss' => 'bıqefeln',
	'timedmedia-download' => 'dosya biyar war',
	'timedmedia-desc-link' => 'derheqê dosyayi de',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'timedmedia-desc' => 'Wóźeński program za dataje Ogg Theora a Vprbis z JavaScriptowym wótegrawakom',
	'timedmedia-short-audio' => 'Ogg $1 awdiodataja, $2',
	'timedmedia-short-video' => 'Ogg $1 wideodataja, $2',
	'timedmedia-short-general' => 'Ogg $1 medijowa dataja, $2',
	'timedmedia-long-audio' => '(Ogg $1 awdiodataja, dłujkosć $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 wideodataja, dłujkosć $2, $4×$5 pikselow, $3)',
	'timedmedia-long-multiplexed' => '(ogg multipleksowa awdio-/wideodataja, $1, dłujkosć $2, $4×$5 pikselow, $3 dogromady)',
	'timedmedia-long-general' => '(Ogg medijowa dataja, dłujkosć $2, $3)',
	'timedmedia-long-error' => '(Njepłaśiwa Ogg-dataja: $1)',
	'timedmedia-no-player-js' => 'Twój wobglědowak jo bóžko pak JavaScript znjemóžnił abo njama njepódpěrany wótegrawak.<br />
Móžoš  <a href="$1">klip ześěgnuś</a> abo <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">wótgrawak ześěgnuś</a>, aby klip w swójom wobglědowaku wótegrał.',
	'timedmedia-more' => 'Wěcej...',
	'timedmedia-dismiss' => 'Zacyniś',
	'timedmedia-download' => 'Dataju ześěgnuś',
	'timedmedia-desc-link' => 'Wó toś tej dataji',
	'timedmedia-oggThumb-version' => 'OggHandler trjeba wersiju $1 oggThumb abo nowšu.',
	'timedmedia-oggThumb-failed' => 'oggThumb njejo mógł wobrazk napóraś.',
);

/** Greek (Ελληνικά)
 * @author Consta
 * @author Dead3y3
 * @author Omnipaedista
 * @author ZaDiak
 */
$messages['el'] = array(
	'timedmedia-desc' => 'Χειριστής για αρχεία Ogg Theora και Vorbis, με αναπαραγωγέα JavaScript',
	'timedmedia-short-audio' => 'Αρχείο ήχου Ogg $1, $2',
	'timedmedia-short-video' => 'Αρχείο βίντεο Ogg $1, $2',
	'timedmedia-short-general' => 'Αρχείο μέσων Ogg $1, $2',
	'timedmedia-long-audio' => '(Αρχείο ήχου Ogg $1, διάρκεια $2, $3)',
	'timedmedia-long-video' => '(Αρχείο βίντεο Ogg $1, διάρκεια $2, $4×$5 pixels, $3)',
	'timedmedia-long-multiplexed' => '(Αρχείο πολυπλεκτικού ήχου/βίντεο Ogg, $1, διάρκεια $2, $4×$5 pixels, $3 ολικά)',
	'timedmedia-long-general' => '(Αρχείο μέσων Ogg, διάρκεια $2, $3)',
	'timedmedia-long-error' => '(Άκυρο αρχείο ogg: $1)',
	'timedmedia-play' => 'Αναπαραγωγή',
	'timedmedia-pause' => 'Παύση',
	'timedmedia-stop' => 'Διακοπή',
	'timedmedia-play-video' => 'Αναπαραγωγή βίντεο',
	'timedmedia-play-sound' => 'Αναπαραγωγή ήχου',
	'timedmedia-no-player' => 'Συγγνώμη, το σύστημά σας δεν φαίνεται να έχει κάποιο υποστηριζόμενο λογισμικό αναπαραγωγής.<br />
Παρακαλώ <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">κατεβάστε ένα πρόγραμμα αναπαραγωγής</a>.',
	'timedmedia-no-xiphqt' => 'Δεν φαίνεται να έχετε το στοιχείο XiphQT για το πρόγραμμα QuickTime.<br />
Το πρόγραμμα QuickTime δεν μπορεί να αναπαράγει αρχεία Ogg χωρίς αυτό το στοιχείο.<br />
Παρακαλώ <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">κατεβάστε το XiphQT</a> ή επιλέξτε ένα άλλο πρόγραμμα αναπαραγωγής.',
	'timedmedia-player-videoElement' => 'Τοπική υποστήριξη φυλλομετρητή',
	'timedmedia-player-oggPlugin' => 'Πρόσθετο φυλλομετρητή',
	'timedmedia-player-thumbnail' => 'Ακίνητη εικόνα μόνο',
	'timedmedia-player-soundthumb' => 'Κανένας αναπαραγωγέας',
	'timedmedia-player-selected' => '(επιλέχθηκε)',
	'timedmedia-use-player' => 'Χρησιμοποίησε αναπαραγωγέα:',
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
	'timedmedia-short-audio' => 'Ogg $1 sondosiero, $2',
	'timedmedia-short-video' => 'Ogg $1 videodosiero, $2',
	'timedmedia-short-general' => 'Media Ogg-dosiero $1, $2',
	'timedmedia-long-audio' => '(Aŭda Ogg-dosiero $1, longeco $2, $3 entute)',
	'timedmedia-long-video' => '(Video Ogg-dosiero $1, longeco $2, $4×$5 pikseloj, $3 entute)',
	'timedmedia-long-multiplexed' => '(Kunigita aŭdio/video Ogg-dosiero, $1, longeco $2, $4×$5 pikseloj, $3 entute)',
	'timedmedia-long-general' => '(Ogg-mediodosiero, longeco $2, $3)',
	'timedmedia-long-error' => '(Malvalida Ogg-dosiero: $1)',
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
	'timedmedia-short-audio' => 'Archivo de sonido Ogg $1, $2',
	'timedmedia-short-video' => 'Archivo de video Ogg $1, $2',
	'timedmedia-short-general' => 'Archivo Ogg $1, $2',
	'timedmedia-long-audio' => '(Archivo de sonido Ogg $1, tamaño $2, $3)',
	'timedmedia-long-video' => '(Archivo de video Ogg $1, tamaño $2, $4×$5 píxeles, $3)',
	'timedmedia-long-multiplexed' => '(Archivo Ogg de audio/video multiplexado, $1, tamaño $2, $4×$5 píxeles, $3 en todo)',
	'timedmedia-long-general' => '(Archivo Ogg. tamaño $2, $3)',
	'timedmedia-long-error' => '(Archivo Ogg no válido: $1)',
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
	'timedmedia-long-error' => '(Vigane timedmedia-fail: $1)',
	'timedmedia-play' => 'Esita',
	'timedmedia-pause' => 'Paus',
	'timedmedia-stop' => 'Peata',
	'timedmedia-play-video' => 'Esita video',
	'timedmedia-play-sound' => 'Esita heli',
	'timedmedia-no-player' => 'Kahjuks ei paista su süsteemis olevat ühtki ühilduvat esitustarkvara.
Palun <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">laadi tarkvara alla</a>.',
	'timedmedia-player-soundthumb' => 'Mängijat ei ole',
	'timedmedia-player-selected' => '(valitud)',
	'timedmedia-use-player' => 'Kasuta mängijat:',
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
	'timedmedia-short-audio' => 'Ogg $1 soinu fitxategia, $2',
	'timedmedia-short-video' => 'Ogg $1 bideo fitxategia, $2',
	'timedmedia-short-general' => 'Ogg $1 media fitxategia, $2',
	'timedmedia-long-audio' => '(Ogg $1 soinu fitxategia, $2 iraupea, $3)',
	'timedmedia-long-error' => '(ogg fitxategi okerra: $1)',
	'timedmedia-play' => 'Hasi',
	'timedmedia-pause' => 'Eten',
	'timedmedia-stop' => 'Gelditu',
	'timedmedia-play-video' => 'Bideoa hasi',
	'timedmedia-play-sound' => 'Soinua hasi',
	'timedmedia-player-soundthumb' => 'Erreproduktorerik ez',
	'timedmedia-player-selected' => '(aukeratua)',
	'timedmedia-use-player' => 'Erabili erreproduktore hau:',
	'timedmedia-more' => 'Gehiago...',
	'timedmedia-dismiss' => 'Itxi',
	'timedmedia-download' => 'Fitxategia jaitsi',
	'timedmedia-desc-link' => 'Fitxategi honen inguruan',
);

/** Persian (فارسی)
 * @author Huji
 */
$messages['fa'] = array(
	'timedmedia-desc' => 'به دست گیرندهٔ پرونده‌های Ogg Theora و Vorbis، با پخش‌کنندهٔ مبتنی بر JavaScript',
	'timedmedia-short-audio' => 'پرونده صوتی Ogg $1، $2',
	'timedmedia-short-video' => 'پرونده تصویری Ogg $1، $2',
	'timedmedia-short-general' => 'پرونده Ogg $1، $2',
	'timedmedia-long-audio' => '(پرونده صوتی Ogg $1، مدت $2، $3)',
	'timedmedia-long-video' => '(پرونده تصویری Ogg $1، مدت $2 ، $4×$5 پیکسل، $3)',
	'timedmedia-long-multiplexed' => '(پرونده صوتی/تصویری پیچیده Ogg، $1، مدت $2، $4×$5 پیکسل، $3 در مجموع)',
	'timedmedia-long-general' => '(پرونده Ogg، مدت $2، $3)',
	'timedmedia-long-error' => '(پرونده Ogg غیرمجاز: $1)',
	'timedmedia-play' => 'پخش',
	'timedmedia-pause' => 'توقف',
	'timedmedia-stop' => 'قطع',
	'timedmedia-play-video' => 'پخش تصویر',
	'timedmedia-play-sound' => 'پخش صوت',
	'timedmedia-no-player' => 'متاسفانه دستگاه شما نرم‌افزار پخش‌کنندهٔ مناسب ندارد. لطفاً <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">یک برنامهٔ پخش‌کننده بارگیری کنید</a>.',
	'timedmedia-no-xiphqt' => 'به نظر نمی‌سرد که شما جزء XiphQT از برنامهٔ QuickTime را داشته باشید. برنامهٔ QuickTime بدون این جزء توان پخش پرونده‌های Ogg را ندارد. لطفاً <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT را بارگیری کنید</a> یا از یک پخش‌کنندهٔ دیگر استفاده کنید.',
	'timedmedia-player-videoElement' => 'پشتیبانی ذاتی مرورگر',
	'timedmedia-player-oggPlugin' => 'افزونهٔ مرورگر',
	'timedmedia-player-thumbnail' => 'فقط تصاویر ثابت',
	'timedmedia-player-soundthumb' => 'فاقد پخش‌کننده',
	'timedmedia-player-selected' => '(انتخاب شده)',
	'timedmedia-use-player' => 'استفاده از پخش‌کننده:',
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
	'timedmedia-short-audio' => 'Ogg $1 -äänitiedosto, $2',
	'timedmedia-short-video' => 'Ogg $1 -videotiedosto, $2',
	'timedmedia-short-general' => 'Ogg $1 -mediatiedosto, $2',
	'timedmedia-long-audio' => '(Ogg $1 -äänitiedosto, $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 -videotiedosto, $2, $4×$5, $3)',
	'timedmedia-long-multiplexed' => '(Ogg-tiedosto (limitetty kuva ja ääni), $1, $2, $4×$5, $3)',
	'timedmedia-long-general' => '(Ogg-tiedosto, $2, $3)',
	'timedmedia-long-error' => '(Kelvoton Ogg-tiedosto: $1)',
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
	'timedmedia-desc' => 'Support pour les médias défilant (vidéo, audio, texte synchronisé) avec transcodage en Ogg Theora/Vorbis',
	'timedmedia-short-audio' => 'Fichier son Ogg $1, $2',
	'timedmedia-short-video' => 'Fichier vidéo Ogg $1, $2',
	'timedmedia-short-general' => 'Fichier média Ogg $1, $2',
	'timedmedia-long-audio' => '(Fichier son Ogg $1, durée $2, $3)',
	'timedmedia-long-video' => '(Fichier vidéo Ogg $1, durée $2, $4×$5 pixels, $3)',
	'timedmedia-long-multiplexed' => '(Fichier multiplexé audio/vidéo Ogg, $1, durée $2, $4×$5 pixels, $3)',
	'timedmedia-long-general' => '(Fichier média Ogg, durée $2, $3)',
	'timedmedia-long-error' => '(Fichier Ogg invalide : $1)',
	'timedmedia-no-player-js' => 'Désolé, votre navigateur doit soit avoir JavaScript désactivé ou n\'a pas un lecteur pris en charge.<br />
Vous pouvez <a href="$1">télécharger le clip</a> ou <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">télécharger un lecteur</a> pour lire le clip dans votre navigateur.',
	'timedmedia-more' => 'Plus…',
	'timedmedia-dismiss' => 'Fermer',
	'timedmedia-download' => 'Télécharger le fichier',
	'timedmedia-desc-link' => 'À propos de ce fichier',
	'timedmedia-oggThumb-version' => 'OggHandler nécessite oggThumb, version $1 ou supérieure.',
	'timedmedia-oggThumb-failed' => 'oggThumb n’a pas réussi à créer la miniature.',
);

/** Franco-Provençal (Arpetan)
 * @author ChrisPtDe
 */
$messages['frp'] = array(
	'timedmedia-desc' => 'Assistance por los fichiérs Ogg Theora et Vorbis, avouéc un liésor JavaScript.',
	'timedmedia-short-audio' => 'Fichiér son Ogg $1, $2',
	'timedmedia-short-video' => 'Fichiér vidèô Ogg $1, $2',
	'timedmedia-short-general' => 'Fichiér multimèdia Ogg $1, $2',
	'timedmedia-long-audio' => '(Fichiér son Ogg $1, temps $2, $3)',
	'timedmedia-long-video' => '(Fichiér vidèô Ogg $1, temps $2, $4×$5 pixèls, $3)',
	'timedmedia-long-multiplexed' => '(Fichiér multiplèxo ôdiô / vidèô Ogg, $1, temps $2, $4×$5 pixèls, $3)',
	'timedmedia-long-general' => '(Fichiér multimèdia Ogg, temps $2, $3)',
	'timedmedia-long-error' => '(Fichiér Ogg envalido : $1)',
	'timedmedia-play' => 'Liére',
	'timedmedia-pause' => 'Pousa',
	'timedmedia-stop' => 'Arrét',
	'timedmedia-play-video' => 'Liére la vidèô',
	'timedmedia-play-sound' => 'Liére lo son',
	'timedmedia-no-player' => 'Dèsolâ, aparament voutron sistèmo at gins de liésor recognu.
Volyéd enstalar <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/fr">yon des liésors recognus</a>.',
	'timedmedia-no-xiphqt' => 'Aparament vos avéd pas lo composent XiphQT por QuickTime.
QuickTime pôt pas liére los fichiérs Ogg sen cél composent.
Volyéd <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/fr">tèlèchargiér XiphQT</a> ou ben chouèsir un ôtro liésor.',
	'timedmedia-player-videoElement' => 'Assistance du navigator nativa',
	'timedmedia-player-oggPlugin' => 'Modulo d’èxtension du navigator',
	'timedmedia-player-thumbnail' => 'Ren que l’émâge fixa',
	'timedmedia-player-soundthumb' => 'Gins de liésor',
	'timedmedia-player-selected' => '(chouèsi)',
	'timedmedia-use-player' => 'Utilisar lo liésor :',
	'timedmedia-more' => 'De ples...',
	'timedmedia-dismiss' => 'Cllôre',
	'timedmedia-download' => 'Tèlèchargiér lo fichiér',
	'timedmedia-desc-link' => 'A propôs de ceti fichiér',
);

/** Friulian (Furlan)
 * @author Klenje
 */
$messages['fur'] = array(
	'timedmedia-desc' => 'Gjestôr pai files Ogg Theora e Vorbis, cuntun riprodutôr JavaScript',
	'timedmedia-short-audio' => 'File audio Ogg $1, $2',
	'timedmedia-short-video' => 'File video Ogg $1, $2',
	'timedmedia-short-general' => 'File multimediâl Ogg $1, $2',
	'timedmedia-long-audio' => '(File audio Ogg $1, durade $2, $3)',
	'timedmedia-long-video' => '(File video Ogg $1, durade $2, dimensions $4×$5 pixels, $3)',
	'timedmedia-long-multiplexed' => '(File audio/video multiplexed Ogg $1, lungjece $2, dimensions $4×$5 pixels, in dut $3)',
	'timedmedia-long-general' => '(File multimediâl Ogg, durade $2, $3)',
	'timedmedia-long-error' => '(File ogg no valit: $1)',
	'timedmedia-play' => 'Riprodûs',
	'timedmedia-pause' => 'Pause',
	'timedmedia-stop' => 'Ferme',
	'timedmedia-play-video' => 'Riprodûs il video',
	'timedmedia-play-sound' => 'Riprodûs il file audio',
	'timedmedia-no-player' => 'Nus displâs ma il to sisteme nol à riprodutôrs software supuartâts.
Par plasê <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">discjame un riprodutôr</a>.',
	'timedmedia-no-xiphqt' => 'Al samee che no tu vedis il component XiphQT par QuickTime.
QuickTime nol pues riprodusi i files Ogg cence di chest component.
Par plasê <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">discjame XiphQT</a> o sielç un altri letôr.',
	'timedmedia-player-videoElement' => 'Supuart sgarfadôr natîf',
	'timedmedia-player-oggPlugin' => 'Plugin sgarfadôr',
	'timedmedia-player-thumbnail' => 'Dome figure fisse',
	'timedmedia-player-soundthumb' => 'Nissun riprodutôr',
	'timedmedia-player-selected' => '(selezionât)',
	'timedmedia-use-player' => 'Dopre il riprodutôr:',
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
	'timedmedia-desc' => 'Manipulador dos ficheiros sincronizados (vídeo, son, texto sincronizado) con transcodificación en Ogg Theora/Vorbis',
	'timedmedia-short-audio' => 'Ficheiro de son Ogg $1, $2',
	'timedmedia-short-video' => 'Ficheiro de vídeo Ogg $1, $2',
	'timedmedia-short-general' => 'Ficheiro multimedia Ogg $1, $2',
	'timedmedia-long-audio' => '(ficheiro de son Ogg $1, duración $2, $3)',
	'timedmedia-long-video' => '(ficheiro de vídeo Ogg $1, duración $2, $4×$5 píxeles, $3)',
	'timedmedia-long-multiplexed' => '(ficheiro de son/vídeo Ogg multiplex, $1, duración $2, $4×$5 píxeles, $3 total)',
	'timedmedia-long-general' => '(ficheiro multimedia Ogg, duración $2, $3)',
	'timedmedia-long-error' => '(ficheiro Ogg non válido: $1)',
	'timedmedia-no-player-js' => 'O seu navegador ten o JavaScript desactivado ou non conta con ningún reprodutor dos soportados.<br />
Pode <a href="$1">descargar o vídeo</a> ou <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">un reprodutor</a> para reproducir o vídeo no seu navegador.',
	'timedmedia-more' => 'Máis...',
	'timedmedia-dismiss' => 'Fechar',
	'timedmedia-download' => 'Descargar o ficheiro',
	'timedmedia-desc-link' => 'Acerca deste ficheiro',
	'timedmedia-oggThumb-version' => 'O OggHandler necesita a versión $1 ou unha posterior do oggThumb.',
	'timedmedia-oggThumb-failed' => 'Houbo un erro por parte do oggThumb ao crear a miniatura.',
);

/** Ancient Greek (Ἀρχαία ἑλληνικὴ)
 * @author Crazymadlover
 * @author Flyax
 * @author Omnipaedista
 */
$messages['grc'] = array(
	'timedmedia-long-error' => '(Ἄκυρα ἀρχεῖα ogg: $1)',
	'timedmedia-play' => 'Ἀναπαράγειν',
	'timedmedia-player-selected' => '(ἐπειλεγμένη)',
	'timedmedia-more' => 'πλέον...',
	'timedmedia-dismiss' => 'Κλῄειν',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 * @author Melancholie
 */
$messages['gsw'] = array(
	'timedmedia-desc' => 'Styyrigsprogramm fir Ogg Theora- un Vorbis-Dateie, mit ere JavaScript-Abspiilsoftware',
	'timedmedia-short-audio' => 'Ogg-$1-Audiodatei, $2',
	'timedmedia-short-video' => 'Ogg-$1-Videodatei, $2',
	'timedmedia-short-general' => 'Ogg-$1-Mediadatei, $2',
	'timedmedia-long-audio' => '(Ogg-$1-Audiodatei, Längi: $2, $3)',
	'timedmedia-long-video' => '(Ogg-$1-Videodatei, Längi: $2, $4×$5 Pixel, $3)',
	'timedmedia-long-multiplexed' => '(Ogg-Audio-/Video-Datei, $1, Längi: $2, $4×$5 Pixel, $3)',
	'timedmedia-long-general' => '(Ogg-Mediadatei, Längi: $2, $3)',
	'timedmedia-long-error' => '(Uugiltigi Ogg-Datei: $1)',
	'timedmedia-more' => 'Meh …',
	'timedmedia-dismiss' => 'Zuemache',
	'timedmedia-download' => 'Datei spychere',
	'timedmedia-desc-link' => 'Iber die Datei',
);

/** Manx (Gaelg)
 * @author MacTire02
 */
$messages['gv'] = array(
	'timedmedia-desc-link' => 'Mychione y choadan shoh',
);

/** Hebrew (עברית)
 * @author Rotem Liss
 * @author Rotemliss
 * @author YaronSh
 */
$messages['he'] = array(
	'timedmedia-desc' => 'מציג מדיה לקובצי Ogg Theora ו־Vorbis, עם נגן JavaScript',
	'timedmedia-short-audio' => 'קובץ שמע $1 של Ogg, $2',
	'timedmedia-short-video' => 'קובץ וידאו $1 של Ogg, $2',
	'timedmedia-short-general' => 'קובץ מדיה $1 של Ogg, $2',
	'timedmedia-long-audio' => '(קובץ שמע $1 של Ogg, באורך $2, $3)',
	'timedmedia-long-video' => '(קובץ וידאו $1 של Ogg, באורך $2, $4×$5 פיקסלים, $3)',
	'timedmedia-long-multiplexed' => '(קובץ מורכב של שמע/וידאו בפורמט Ogg, $1, באורך $2, $4×$5 פיקסלים, $3 בסך הכל)',
	'timedmedia-long-general' => '(קובץ מדיה של Ogg, באורך $2, $3)',
	'timedmedia-long-error' => '(קובץ ogg בלתי תקין: $1)',
	'timedmedia-play' => 'נגן',
	'timedmedia-pause' => 'הפסק',
	'timedmedia-stop' => 'עצור',
	'timedmedia-play-video' => 'נגן וידאו',
	'timedmedia-play-sound' => 'נגן שמע',
	'timedmedia-no-player' => 'מצטערים, נראה שהמערכת שלכם אינה כוללת תוכנת נגן נתמכת. אנא <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">הורידו נגן</a>.',
	'timedmedia-no-xiphqt' => 'נראה שלא התקנתם את רכיב XiphQT של QuickTime, אך QuickTime אינו יכול לנגן קובצי Ogg בלי רכיב זה. אנא <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">הורידו את XiphQT</a> או בחרו נגן אחר.',
	'timedmedia-player-videoElement' => 'תמיכה טבעית של הדפדפן',
	'timedmedia-player-oggPlugin' => 'תוסף לדפדפן',
	'timedmedia-player-thumbnail' => 'עדיין תמונה בלבד',
	'timedmedia-player-soundthumb' => 'אין נגן',
	'timedmedia-player-selected' => '(נבחר)',
	'timedmedia-use-player' => 'שימוש בנגן:',
	'timedmedia-more' => 'עוד…',
	'timedmedia-dismiss' => 'סגירה',
	'timedmedia-download' => 'הורדת הקובץ',
	'timedmedia-desc-link' => 'אודות הקובץ',
);

/** Hindi (हिन्दी)
 * @author Kaustubh
 * @author Shyam
 */
$messages['hi'] = array(
	'timedmedia-desc' => 'ऑग थियोरा और वॉर्बिस फ़ाईल्सके लिये चालक, जावास्क्रीप्ट प्लेयर के साथ',
	'timedmedia-short-audio' => 'ऑग $1 ध्वनी फ़ाईल, $2',
	'timedmedia-short-video' => 'ऑग $1 चलतचित्र फ़ाईल, $2',
	'timedmedia-short-general' => 'ऑग $1 मीडिया फ़ाईल, $2',
	'timedmedia-long-audio' => '(ऑग $1 ध्वनी फ़ाईल, लंबाई $2, $3)',
	'timedmedia-long-video' => '(ऑग $1 चलतचित्र फ़ाईल, लंबाई $2, $4×$5 पीक्सेल्स, $3)',
	'timedmedia-long-multiplexed' => '(ऑग ध्वनी/चित्र फ़ाईल, $1, लंबाई $2, $4×$5 पिक्सेल्स, $3 कुल)',
	'timedmedia-long-general' => '(ऑग मीडिया फ़ाईल, लंबाई $2, $3)',
	'timedmedia-long-error' => '(गलत ऑग फ़ाईल: $1)',
	'timedmedia-play' => 'शुरू करें',
	'timedmedia-pause' => 'विराम',
	'timedmedia-stop' => 'रोकें',
	'timedmedia-play-video' => 'विडियो शुरू करें',
	'timedmedia-play-sound' => 'ध्वनी चलायें',
	'timedmedia-no-player' => 'क्षमा करें, आपके तंत्र में कोई प्रमाणिक चालक सॉफ्टवेयर दर्शित नहीं हो रहा है।
कृपया <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">एक चालक डाउनलोड करें</a>।',
	'timedmedia-no-xiphqt' => 'आपके पास QuickTime के लिए XiphQT घटक प्रतीत नहीं हो रहा है।
QuickTime बिना इस घटक के Ogg files चलने में असमर्थ है।
कृपया <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT डाउनलोड करें</a> अथवा अन्य चालक चुनें।',
	'timedmedia-player-videoElement' => '<video> घटक',
	'timedmedia-player-oggPlugin' => 'ऑग प्लगीन',
	'timedmedia-player-thumbnail' => 'सिर्फ स्थिर चित्र',
	'timedmedia-player-soundthumb' => 'प्लेअर नहीं हैं',
	'timedmedia-player-selected' => '(चुने हुए)',
	'timedmedia-use-player' => 'यह प्लेअर इस्तेमाल करें:',
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
	'timedmedia-short-audio' => 'Ogg $1 zvučna datoteka, $2',
	'timedmedia-short-video' => 'Ogg $1 video datoteka, $2',
	'timedmedia-short-general' => 'Ogg $1 medijska datoteka, $2',
	'timedmedia-long-audio' => '(Ogg $1 zvučna datoteka, duljine $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 video datoteka, duljine $2, $4x$5 piksela, $3)',
	'timedmedia-long-multiplexed' => '(Ogg multipleksirana zvučna/video datoteka, $1, duljine $2, $4×$5 piksela, $3 ukupno)',
	'timedmedia-long-general' => '(Ogg medijska datoteka, duljine $2, $3)',
	'timedmedia-long-error' => '(nevaljana ogg datoteka: $1)',
	'timedmedia-play' => 'Pokreni',
	'timedmedia-pause' => 'Pauziraj',
	'timedmedia-stop' => 'Zaustavi',
	'timedmedia-play-video' => 'Pokreni video',
	'timedmedia-play-sound' => 'Sviraj zvuk',
	'timedmedia-no-player' => "Oprostite, izgleda da Vaš operacijski sustav nema instalirane medijske preglednike. Molimo <a href=\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\">instalirajte medijski preglednik (''player'')</a>.",
	'timedmedia-no-xiphqt' => "Nemate instaliranu XiphQT komponentu za QuickTime (ili je neispravno instalirana). QuickTime ne može pokretati Ogg datoteke bez ove komponente. Molimo <a href=\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\">instalirajte XiphQT</a> ili izaberite drugi preglednik (''player'').",
	'timedmedia-player-videoElement' => 'Ugrađena podrška za preglednik',
	'timedmedia-player-oggPlugin' => 'Plugin preglednika',
	'timedmedia-player-vlc-activex' => 'VLC (ActiveX kontrola)',
	'timedmedia-player-thumbnail' => 'Samo (nepokretne) slike',
	'timedmedia-player-soundthumb' => 'Nema preglednika',
	'timedmedia-player-selected' => '(odabran)',
	'timedmedia-use-player' => "Rabi preglednik (''player''):",
	'timedmedia-more' => 'Više...',
	'timedmedia-dismiss' => 'Zatvori',
	'timedmedia-download' => 'Snimi datoteku',
	'timedmedia-desc-link' => 'O ovoj datoteci',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Dundak
 * @author Michawiki
 */
$messages['hsb'] = array(
	'timedmedia-desc' => 'Wodźenski program za Timed Media (widejo, awdio, timedText) z překodowanjom do Ogg Theora/Vorbis',
	'timedmedia-short-audio' => 'Awdiodataja Ogg $1, $2',
	'timedmedia-short-video' => 'Widejodataja Ogg $1, $2',
	'timedmedia-short-general' => 'Ogg medijowa dataja $1, $2',
	'timedmedia-long-audio' => '(Ogg-awdiodataja $1, dołhosć: $2, $3)',
	'timedmedia-long-video' => '(Ogg-widejodataja $1, dołhosć: $2, $4×$5 pikselow, $3)',
	'timedmedia-long-multiplexed' => '(Ogg multipleksna awdio-/widejodataja, $1, dołhosć: $2, $4×$5 pikselow, $3)',
	'timedmedia-long-general' => '(Ogg medijowa dataja, dołhosć: $2, $3)',
	'timedmedia-long-error' => '(Njepłaćiwa Ogg-dataja: $1)',
	'timedmedia-no-player-js' => 'Twój wobhladowak je pak JavaScript znjemóžnił pak nima podpěrowany wothrawak.<br />
Móžeš <a href="$1">klip sćahnyć</a> abo <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">wothrawak sćahnyć</a, zo by klip w swojim wobhladowaku wothrawał.',
	'timedmedia-more' => 'Wjace ...',
	'timedmedia-dismiss' => 'Začinić',
	'timedmedia-download' => 'Dataju sćahnyć',
	'timedmedia-desc-link' => 'Wo tutej dataji',
	'timedmedia-oggThumb-version' => 'OggHandler trjeba wersiju $1 oggThumb abo nowšu.',
	'timedmedia-oggThumb-failed' => 'oggThumb njemóžeše wobrazk wutworić.',
);

/** Haitian (Kreyòl ayisyen)
 * @author Masterches
 */
$messages['ht'] = array(
	'timedmedia-play' => 'Jwe',
	'timedmedia-pause' => 'Poz',
	'timedmedia-stop' => 'Stope',
);

/** Hungarian (Magyar)
 * @author Dani
 * @author Glanthor Reviol
 * @author Tgr
 */
$messages['hu'] = array(
	'timedmedia-desc' => 'JavaScript nyelven írt lejátszó Ogg Theora és Vorbis fájlokhoz',
	'timedmedia-short-audio' => 'Ogg $1 hangfájl, $2',
	'timedmedia-short-video' => 'Ogg $1 videofájl, $2',
	'timedmedia-short-general' => 'Ogg $1 médiafájl, $2',
	'timedmedia-long-audio' => '(Ogg $1 hangfájl, hossza: $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 videófájl, hossza $2, $4×$5 képpont, $3)',
	'timedmedia-long-multiplexed' => '(Ogg egyesített audió- és videófájl, $1, hossz: $2, $4×$5 képpont, $3 összesen)',
	'timedmedia-long-general' => '(Ogg médiafájl, hossza: $2, $3)',
	'timedmedia-long-error' => '(Érvénytelen ogg fájl: $1)',
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
	'timedmedia-desc' => 'Gestor pro le files Ogg Theora e Vorbis, con reproductor JavaScript',
	'timedmedia-short-audio' => 'File audio Ogg $1, $2',
	'timedmedia-short-video' => 'File video Ogg $1, $2',
	'timedmedia-short-general' => 'File media Ogg $1, $2',
	'timedmedia-long-audio' => '(File audio Ogg $1, duration $2, $3)',
	'timedmedia-long-video' => '(File video Ogg $1, duration $2, $4×$5 pixel, $3)',
	'timedmedia-long-multiplexed' => '(File multiplexate audio/video Ogg, $1, duration $2, $4×$5 pixel, $3 in total)',
	'timedmedia-long-general' => '(File media Ogg, duration $2, $3)',
	'timedmedia-long-error' => '(File Ogg invalide: $1)',
	'timedmedia-no-player-js' => 'Pardono, tu systema o ha JavaScript disactivate o non ha un reproductor supportate.<br />
Tu pote <a href="$1">discargar le clip</a> o <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">discargar un reproductor</a> pro reproducer le clip in tu navigator.',
	'timedmedia-more' => 'Plus…',
	'timedmedia-dismiss' => 'Clauder',
	'timedmedia-download' => 'Discargar file',
	'timedmedia-desc-link' => 'A proposito de iste file',
	'timedmedia-oggThumb-version' => 'OggHandler require oggThumb version $1 o plus recente.',
	'timedmedia-oggThumb-failed' => 'oggThumb ha fallite de crear le miniatura.',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 * @author Irwangatot
 * @author IvanLanin
 * @author Rex
 */
$messages['id'] = array(
	'timedmedia-desc' => 'Menangani berkas Ogg Theora dan Vorbis dengan pemutar JavaScript',
	'timedmedia-short-audio' => 'Berkas suara $1 ogg, $2',
	'timedmedia-short-video' => 'Berkas video $1 ogg, $2',
	'timedmedia-short-general' => 'Berkas media $1 ogg, $2',
	'timedmedia-long-audio' => '(Berkas suara $1 ogg, panjang $2, $3)',
	'timedmedia-long-video' => '(Berkas video $1 ogg, panjang $2, $4×$5 piksel, $3)',
	'timedmedia-long-multiplexed' => '(Berkas audio/video multiplexed ogg, $1, panjang $2, $4×$5 piksel, $3 keseluruhan)',
	'timedmedia-long-general' => '(Berkas media ogg, panjang $2, $3)',
	'timedmedia-long-error' => '(Berkas ogg tak valid: $1)',
	'timedmedia-play' => 'Mainkan',
	'timedmedia-pause' => 'Jeda',
	'timedmedia-stop' => 'Berhenti',
	'timedmedia-play-video' => 'Putar video',
	'timedmedia-play-sound' => 'Putar suara',
	'timedmedia-no-player' => 'Maaf, sistem Anda tampaknya tak memiliki satupun perangkat lunak pemutar yang mendukung.
Silakan <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">mengunduh salah satu pemutar</a>.',
	'timedmedia-no-xiphqt' => 'Tampaknya Anda tak memiliki komponen XiphQT untuk QuickTime. QuickTime tak dapat memutar berkas Ogg tanpa komponen ini. Silakan <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">mengunduh XiphQT</a> atau pilih pemutar lain.',
	'timedmedia-player-videoElement' => 'elemen <video>',
	'timedmedia-player-oggPlugin' => 'plugin Ogg',
	'timedmedia-player-thumbnail' => 'Hanya gambar statis',
	'timedmedia-player-soundthumb' => 'Tak ada pemutar',
	'timedmedia-player-selected' => '(terpilih)',
	'timedmedia-use-player' => 'Gunakan pemutar:',
	'timedmedia-more' => 'Lainnya...',
	'timedmedia-dismiss' => 'Tutup',
	'timedmedia-download' => 'Unduh berkas',
	'timedmedia-desc-link' => 'Mengenai berkas ini',
);

/** Ido (Ido)
 * @author Malafaya
 */
$messages['io'] = array(
	'timedmedia-long-error' => '(Ne-valida timedmedia-arkivo: $1)',
	'timedmedia-player-selected' => '(selektita)',
	'timedmedia-more' => 'Plus…',
	'timedmedia-dismiss' => 'Klozar',
	'timedmedia-desc-link' => 'Pri ca arkivo',
);

/** Icelandic (Íslenska)
 * @author S.Örvarr.S
 * @author Spacebirdy
 */
$messages['is'] = array(
	'timedmedia-play' => 'Spila',
	'timedmedia-pause' => 'gera hlé',
	'timedmedia-stop' => 'Stöðva',
	'timedmedia-play-video' => 'Spila myndband',
	'timedmedia-play-sound' => 'Spila hljóð',
	'timedmedia-player-soundthumb' => 'Enginn spilari',
	'timedmedia-player-selected' => '(valið)',
	'timedmedia-use-player' => 'Nota spilara:',
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
	'timedmedia-short-audio' => 'File audio Ogg $1, $2',
	'timedmedia-short-video' => 'File video Ogg $1, $2',
	'timedmedia-short-general' => 'File multimediale Ogg $1, $2',
	'timedmedia-long-audio' => '(File audio Ogg $1, durata $2, $3)',
	'timedmedia-long-video' => '(File video Ogg $1, durata $2, dimensioni $4×$5 pixel, $3)',
	'timedmedia-long-multiplexed' => '(File audio/video multiplexed Ogg $1, durata $2, dimensioni $4×$5 pixel, complessivamente $3)',
	'timedmedia-long-general' => '(File multimediale Ogg, durata $2, $3)',
	'timedmedia-long-error' => '(File ogg non valido: $1)',
	'timedmedia-play' => 'Riproduci',
	'timedmedia-pause' => 'Pausa',
	'timedmedia-stop' => 'Ferma',
	'timedmedia-play-video' => 'Riproduci il filmato',
	'timedmedia-play-sound' => 'Riproduci il file sonoro',
	'timedmedia-no-player' => 'Siamo spiacenti, ma non risulta installato alcun software di riproduzione compatibile. Si prega di <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">scaricare un lettore</a> adatto.',
	'timedmedia-no-xiphqt' => 'Non risulta installato il componente XiphQT di QuickTime. Senza tale componente non è possibile la riproduzione di file Ogg con QuickTime. Si prega di <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">scaricare XiphQT</a> o scegliere un altro lettore.',
	'timedmedia-player-videoElement' => 'Supporto browser nativo',
	'timedmedia-player-oggPlugin' => 'Plugin browser',
	'timedmedia-player-thumbnail' => 'Solo immagini fisse',
	'timedmedia-player-soundthumb' => 'Nessun lettore',
	'timedmedia-player-selected' => '(selezionato)',
	'timedmedia-use-player' => 'Usa il lettore:',
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
	'timedmedia-short-audio' => 'Ogg $1 音声ファイル、$2',
	'timedmedia-short-video' => 'Ogg $1 動画ファイル、$2',
	'timedmedia-short-general' => 'Ogg $1 メディアファイル、$2',
	'timedmedia-long-audio' => '(Ogg $1 音声ファイル、長さ $2、$3)',
	'timedmedia-long-video' => '(Ogg $1 動画ファイル、長さ $2、$4×$5px、$3)',
	'timedmedia-long-multiplexed' => '(Ogg 多重音声/動画ファイル、$1、長さ $2、$4×$5 ピクセル、$3)',
	'timedmedia-long-general' => '(Ogg メディアファイル、長さ $2、$3)',
	'timedmedia-long-error' => '(無効な Ogg ファイル: $1)',
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
	'timedmedia-short-audio' => 'Ogg $1 sond file, $2',
	'timedmedia-short-video' => 'Ogg $1 video file, $2',
	'timedmedia-short-general' => 'Ogg $1 media file, $2',
	'timedmedia-long-audio' => '(Ogg $1 sond file, duråsje $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 video file, duråsje $2, $4×$5 piksel, $3)',
	'timedmedia-long-multiplexed' => '(Ogg multipleksen audio/video file, $1, duråsje $2, $4×$5 piksler, $3 åverål)',
	'timedmedia-long-general' => '(Ogg $1 media file, duråsje $2, $3)',
	'timedmedia-long-error' => '(Ugyldegt ogg file: $2)',
	'timedmedia-play' => 'Spæl',
	'timedmedia-pause' => 'Pås',
	'timedmedia-stop' => 'Ståp',
	'timedmedia-play-video' => 'Spæl video',
	'timedmedia-play-sound' => 'Spæl sond',
	'timedmedia-no-player' => 'Unskyld, deres sistæm dä ekke appiære til har søm understønenge spæler softwær. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Nærlæĝ en spæler</a>.',
	'timedmedia-no-xiphqt' => 'Du däst ekke appiær til har æ XiphQT kompånent før QuickTime. QuickTime ken ekke spæl Ogg filer veud dette kompånent. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Nærlæĝ XiphQT</a> æller vælg\'en andes spæler.',
	'timedmedia-player-videoElement' => '<video> ælement',
	'timedmedia-player-oggPlugin' => 'Ogg plugin',
	'timedmedia-player-thumbnail' => 'Stil billet ålen',
	'timedmedia-player-soundthumb' => 'Ekke spæler',
	'timedmedia-player-selected' => '(sælektærn)',
	'timedmedia-use-player' => 'Brug spæler:',
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
	'timedmedia-short-audio' => 'Berkas swara $1 ogg, $2',
	'timedmedia-short-video' => 'Berkas vidéo $1 ogg, $2',
	'timedmedia-short-general' => 'Berkas média $1 ogg, $2',
	'timedmedia-long-audio' => '(Berkas swara $1 ogg, dawané $2, $3)',
	'timedmedia-long-video' => '(Berkas vidéo $1 ogg, dawané $2, $4×$5 piksel, $3)',
	'timedmedia-long-multiplexed' => '(Berkas audio/vidéo multiplexed ogg, $1, dawané $2, $4×$5 piksel, $3 gunggungé)',
	'timedmedia-long-general' => '(Berkas média ogg, dawané $2, $3)',
	'timedmedia-long-error' => '(Berkas ogg ora absah: $1)',
	'timedmedia-play' => 'Main',
	'timedmedia-pause' => 'Lèrèn',
	'timedmedia-stop' => 'Mandeg',
	'timedmedia-play-video' => 'Main vidéo',
	'timedmedia-play-sound' => 'Main swara',
	'timedmedia-no-player' => 'Nuwun sèwu, sistém panjenengan katoné ora ndarbèni siji-sijia piranti empuk sing didukung.
Mangga <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ngundhuh salah siji piranti pamain</a>.',
	'timedmedia-no-xiphqt' => 'Katoné panjenengan ora ana komponèn XiphQT kanggo QuickTime.
QuickTime ora bisa mainaké berkas-berkas Ogg tanpa komponèn iki.
Please <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ngundhuh XiphQT</a> utawa milih piranti pamain liya.',
	'timedmedia-player-videoElement' => 'Dhukungan browser asli',
	'timedmedia-player-oggPlugin' => "''Plugin browser''",
	'timedmedia-player-thumbnail' => 'Namung gambar statis waé',
	'timedmedia-player-soundthumb' => 'Ora ana piranti pamain',
	'timedmedia-player-selected' => '(dipilih)',
	'timedmedia-use-player' => 'Nganggo piranti pamain:',
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
	'timedmedia-short-video' => 'Ogg $1 ვიდეო ფაილი, $2',
	'timedmedia-short-general' => 'Ogg $1 მედია ფაილი, $2',
	'timedmedia-play' => 'თამაში',
	'timedmedia-pause' => 'პაუზა',
	'timedmedia-stop' => 'შეჩერება',
	'timedmedia-play-video' => 'ვიდეოს ჩართვა',
	'timedmedia-play-sound' => 'ხმის ტამაში',
	'timedmedia-player-soundthumb' => 'No player',
	'timedmedia-player-selected' => '(არჩეულია)',
	'timedmedia-more' => 'მეტი...',
	'timedmedia-dismiss' => 'დახურვა',
	'timedmedia-download' => 'ფაილის ჩამოტვირთვა',
	'timedmedia-desc-link' => 'ამ ფაილის შესახებ',
);

/** Kazakh (Arabic script) (‫قازاقشا (تٴوتە)‬) */
$messages['kk-arab'] = array(
	'timedmedia-short-audio' => 'Ogg $1 دىبىس فايلى, $2',
	'timedmedia-short-video' => 'Ogg $1 بەينە فايلى, $2',
	'timedmedia-short-general' => 'Ogg $1 تاسپا فايلى, $2',
	'timedmedia-long-audio' => '(Ogg $1 دىبىس فايلى, ۇزاقتىعى $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 بەينە فايلى, ۇزاقتىعى $2, $4 × $5 پىيكسەل, $3)',
	'timedmedia-long-multiplexed' => '(Ogg قۇرامدى دىبىس/بەينە فايلى, $1, ۇزاقتىعى $2, $4 × $5 پىيكسەل, $3 نە بارلىعى)',
	'timedmedia-long-general' => '(Ogg تاسپا فايلى, ۇزاقتىعى $2, $3)',
	'timedmedia-long-error' => '(جارامسىز ogg فايلى: $1)',
	'timedmedia-play' => 'ويناتۋ',
	'timedmedia-pause' => 'ايالداتۋ',
	'timedmedia-stop' => 'توقتاتۋ',
	'timedmedia-play-video' => 'بەينەنى ويناتۋ',
	'timedmedia-play-sound' => 'دىبىستى ويناتۋ',
	'timedmedia-no-player' => 'عافۋ ەتىڭىز, جۇيەڭىزدە ەش سۇيەمەلدەگەن ويناتۋ باعدارلامالىق قامتاماسىزداندىرعىش ورناتىلماعان. <a href="http://www.java.com/en/download/manual.jsp">Java</a> بۋماسىن ورناتىپ شىعىڭىز.',
	'timedmedia-no-xiphqt' => 'QuickTime ويناتقىشىڭىزدىڭ XiphQT دەگەن قۇراشى جوق سىيياقتى. بۇل قۇراشىسىز Ogg فايلدارىن QuickTime ويناتا المايدى. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT قۇراشىن</a> نە باسقا ويناتقىشتى جۇكتەڭىز.',
	'timedmedia-player-videoElement' => '<video> داناسى',
	'timedmedia-player-oggPlugin' => 'Ogg قوسىمشا باعدارلاماسى',
	'timedmedia-player-thumbnail' => 'تەك ستوپ-كادر',
	'timedmedia-player-soundthumb' => 'ويناتقىشسىز',
	'timedmedia-player-selected' => '(بولەكتەلگەن)',
	'timedmedia-use-player' => 'ويناتقىش پايدالانۋى:',
	'timedmedia-more' => 'كوبىرەك...',
	'timedmedia-dismiss' => 'جابۋ',
	'timedmedia-download' => 'فايلدى جۇكتەۋ',
	'timedmedia-desc-link' => 'بۇل فايل تۋرالى',
);

/** Kazakh (Cyrillic) (Қазақша (Cyrillic)) */
$messages['kk-cyrl'] = array(
	'timedmedia-short-audio' => 'Ogg $1 дыбыс файлы, $2',
	'timedmedia-short-video' => 'Ogg $1 бейне файлы, $2',
	'timedmedia-short-general' => 'Ogg $1 таспа файлы, $2',
	'timedmedia-long-audio' => '(Ogg $1 дыбыс файлы, ұзақтығы $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 бейне файлы, ұзақтығы $2, $4 × $5 пиксел, $3)',
	'timedmedia-long-multiplexed' => '(Ogg құрамды дыбыс/бейне файлы, $1, ұзақтығы $2, $4 × $5 пиксел, $3 не барлығы)',
	'timedmedia-long-general' => '(Ogg таспа файлы, ұзақтығы $2, $3)',
	'timedmedia-long-error' => '(Жарамсыз ogg файлы: $1)',
	'timedmedia-play' => 'Ойнату',
	'timedmedia-pause' => 'Аялдату',
	'timedmedia-stop' => 'Тоқтату',
	'timedmedia-play-video' => 'Бейнені ойнату',
	'timedmedia-play-sound' => 'Дыбысты ойнату',
	'timedmedia-no-player' => 'Ғафу етіңіз, жүйеңізде еш сүйемелдеген ойнату бағдарламалық қамтамасыздандырғыш орнатылмаған. <a href="http://www.java.com/en/download/manual.jsp">Java</a> бумасын орнатып шығыңыз.',
	'timedmedia-no-xiphqt' => 'QuickTime ойнатқышыңыздың XiphQT деген құрашы жоқ сияқты. Бұл құрашысыз Ogg файлдарын QuickTime ойната алмайды. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT құрашын</a> не басқа ойнатқышты жүктеңіз.',
	'timedmedia-player-videoElement' => '<video> данасы',
	'timedmedia-player-oggPlugin' => 'Ogg қосымша бағдарламасы',
	'timedmedia-player-thumbnail' => 'Тек стоп-кадр',
	'timedmedia-player-soundthumb' => 'Ойнатқышсыз',
	'timedmedia-player-selected' => '(бөлектелген)',
	'timedmedia-use-player' => 'Ойнатқыш пайдалануы:',
	'timedmedia-more' => 'Көбірек...',
	'timedmedia-dismiss' => 'Жабу',
	'timedmedia-download' => 'Файлды жүктеу',
	'timedmedia-desc-link' => 'Бұл файл туралы',
);

/** Kazakh (Latin) (Қазақша (Latin)) */
$messages['kk-latn'] = array(
	'timedmedia-short-audio' => 'Ogg $1 dıbıs faýlı, $2',
	'timedmedia-short-video' => 'Ogg $1 beýne faýlı, $2',
	'timedmedia-short-general' => 'Ogg $1 taspa faýlı, $2',
	'timedmedia-long-audio' => '(Ogg $1 dıbıs faýlı, uzaqtığı $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 beýne faýlı, uzaqtığı $2, $4 × $5 pïksel, $3)',
	'timedmedia-long-multiplexed' => '(Ogg quramdı dıbıs/beýne faýlı, $1, uzaqtığı $2, $4 × $5 pïksel, $3 ne barlığı)',
	'timedmedia-long-general' => '(Ogg taspa faýlı, uzaqtığı $2, $3)',
	'timedmedia-long-error' => '(Jaramsız ogg faýlı: $1)',
	'timedmedia-play' => 'Oýnatw',
	'timedmedia-pause' => 'Ayaldatw',
	'timedmedia-stop' => 'Toqtatw',
	'timedmedia-play-video' => 'Beýneni oýnatw',
	'timedmedia-play-sound' => 'Dıbıstı oýnatw',
	'timedmedia-no-player' => 'Ğafw etiñiz, jüýeñizde eş süýemeldegen oýnatw bağdarlamalıq qamtamasızdandırğış ornatılmağan. <a href="http://www.java.com/en/download/manual.jsp">Java</a> bwmasın ornatıp şığıñız.',
	'timedmedia-no-xiphqt' => 'QuickTime oýnatqışıñızdıñ XiphQT degen quraşı joq sïyaqtı. Bul quraşısız Ogg faýldarın QuickTime oýnata almaýdı. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT quraşın</a> ne basqa oýnatqıştı jükteñiz.',
	'timedmedia-player-videoElement' => '<video> danası',
	'timedmedia-player-oggPlugin' => 'Ogg qosımşa bağdarlaması',
	'timedmedia-player-thumbnail' => 'Tek stop-kadr',
	'timedmedia-player-soundthumb' => 'Oýnatqışsız',
	'timedmedia-player-selected' => '(bölektelgen)',
	'timedmedia-use-player' => 'Oýnatqış paýdalanwı:',
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
	'timedmedia-short-audio' => 'ឯកសារ សំឡេង Ogg $1, $2',
	'timedmedia-short-video' => 'ឯកសារវីដេអូ Ogg $1, $2',
	'timedmedia-short-general' => 'ឯកសារមេឌាOgg $1, $2',
	'timedmedia-long-audio' => '(ឯកសារសំឡេងប្រភេទOgg $1, រយៈពេល$2 និងទំហំ$3)',
	'timedmedia-long-video' => '(ឯកសារវីដេអូប្រភេទOgg $1, រយៈពេល$2, $4×$5px, $3)',
	'timedmedia-long-multiplexed' => '(ឯកសារអូឌីយ៉ូ/វីដេអូចម្រុះប្រភេទOgg , $1, រយៈពេល$2, $4×$5px, ប្រហែល$3)',
	'timedmedia-long-general' => '(ឯកសារមេឌាប្រភេទOgg, រយៈពេល$2, $3)',
	'timedmedia-long-error' => '(ឯកសារ ogg មិនមាន សុពលភាព ៖ $1)',
	'timedmedia-play' => 'លេង',
	'timedmedia-pause' => 'ផ្អាក',
	'timedmedia-stop' => 'ឈប់',
	'timedmedia-play-video' => 'លេងវីដេអូ',
	'timedmedia-play-sound' => 'បន្លឺសំឡេង',
	'timedmedia-no-player' => 'សូមអភ័យទោស! ប្រព័ន្ធដំណើរការរបស់អ្នក ហាក់បីដូចជាមិនមានកម្មវិធី ណាមួយសម្រាប់លេងទេ។ សូម <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ទាញយកកម្មវិធី សម្រាប់លេងនៅទីនេះ</a> ។',
	'timedmedia-no-xiphqt' => 'មិនឃើញមាន អង្គផ្សំ XiphQT សម្រាប់ QuickTime។ QuickTime មិនអាចអាន ឯកសារ ដោយ គ្មាន អង្គផ្សំនេះ។ ទាញយក <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download"> និង ដំឡើង XiphQT</a> ឬ ជ្រើសរើស ឧបករណ៍អាន ផ្សេង ។',
	'timedmedia-player-videoElement' => 'Native browser support',
	'timedmedia-player-oggPlugin' => 'កម្មវិធីជំនួយ​របស់​កម្មវិធីរុករក',
	'timedmedia-player-thumbnail' => 'នៅតែជារូបភាព',
	'timedmedia-player-soundthumb' => 'មិនមានឧបករណ៍លេងទេ',
	'timedmedia-player-selected' => '(បានជ្រើសយក)',
	'timedmedia-use-player' => 'ប្រើប្រាស់ឧបករណ៍លេង៖',
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
	'timedmedia-short-audio' => 'Ogg $1 소리 파일, $2',
	'timedmedia-short-video' => 'Ogg $1 영상 파일, $2',
	'timedmedia-short-general' => 'Ogg $1 미디어 파일, $2',
	'timedmedia-long-audio' => '(Ogg $1 소리 파일, 길이 $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 영상 파일, 길이 $2, $4×$5 픽셀, $3)',
	'timedmedia-long-multiplexed' => '(Ogg 다중 소리/영상 파일, $1, 길이 $2, $4×$5 픽셀, 대략 $3)',
	'timedmedia-long-general' => '(Ogg 미디어 파일, 길이 $2, $3)',
	'timedmedia-long-error' => '(잘못된 ogg 파일: $1)',
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
	'timedmedia-desc' => 'En Projamm (<i lang="en">handler</i>) för <i lang="en">Ogg Theora</i> un <i lang="en">Ogg Vorbis</i> Dateie, met enem Javaskrip Afspiller.',
	'timedmedia-short-audio' => '<i lang="en">Ogg $1</i> Tondatei, $2',
	'timedmedia-short-video' => '<i lang="en">Ogg $1</i> Viddejodatei, $2',
	'timedmedia-short-general' => '<i lang="en">Ogg $1</i> Medijedatei, $2',
	'timedmedia-long-audio' => '(<i lang="en">Ogg $1</i> Tondatei fum Ömfang $2, $3)',
	'timedmedia-long-video' => '(<i lang="en">Ogg $1</i> Viddejodatei fum Ömfang $2 un {{PLURAL:$4|ein Pixel|$4 Pixelle|kei Pixel}} × {{PLURAL:$5|ei Pixel|$4 Pixelle|kei Pixel}}, $3)',
	'timedmedia-long-multiplexed' => '(<i lang="en">Ogg</i> jemultipex Ton- un Viddejodatei, $1, fum Ömfang $2 un {{PLURAL:$4|ein Pixel|$4 Pixelle|kei Pixel}} × {{PLURAL:$5|ei Pixel|$4 Pixelle|kei Pixel}}, $3 ennsjesammp)',
	'timedmedia-long-general' => '(<i lang="en">Ogg</i> Medijedatei fum Ömfang $2, $3)',
	'timedmedia-long-error' => '(ene kapodde <i lang="en">Ogg</i> Datei: $1)',
	'timedmedia-play' => 'Loßläje!',
	'timedmedia-pause' => 'Aanhallde!',
	'timedmedia-stop' => 'Ophüre!',
	'timedmedia-play-video' => 'Dun der Viddejo affshpelle',
	'timedmedia-play-sound' => 'Dä Ton afshpelle',
	'timedmedia-no-player' => 'Deijt mer leid, süüd_esu uß, wi wann Dinge Kompjutor kei
Affspellprojramm hät, wat mer öngerstoze däte.
Beß esu joot, un <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">donn e Affspellprojramm erunger lade</a>.',
	'timedmedia-no-xiphqt' => 'Deijt mer leid, süüd_esu uß, wi wann Dinge Kompjutor nit
dat XiphQT Affspellprojrammstöck för <i lang="en">QuickTime</i> hät,
ävver <i lang="en">QuickTime</i> kann <i lang="en">Ogg</i>-Dateie
der oohne nit affspelle.
Beß esu joot, un <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">donn dat XiphQT erunger lade</a>,
udder sök Der en annder Affspellprojramm uß.',
	'timedmedia-player-videoElement' => 'Ongerstözung för Brauser',
	'timedmedia-player-oggPlugin' => 'Brauser <i lang="en">Plug-In</i>',
	'timedmedia-player-cortado' => 'Cortado (Java)',
	'timedmedia-player-vlc-mozilla' => 'VLC',
	'timedmedia-player-vlc-activex' => 'VLC (<i lang="en">ActiveX</i>)',
	'timedmedia-player-quicktime-mozilla' => '<i lang="en">QuickTime</i>',
	'timedmedia-player-quicktime-activex' => '<i lang="en">QuickTime</i> (<i lang="en">ActiveX</i>)',
	'timedmedia-player-totem' => 'Totem',
	'timedmedia-player-kmplayer' => 'KM<i lang="en">Player</i>',
	'timedmedia-player-kaffeine' => '<i lang="en">Kaffeine</i>',
	'timedmedia-player-mplayerplug-in' => '<i lang="en">mplayerplug-in</i>',
	'timedmedia-player-thumbnail' => 'Bloß e Standbeld',
	'timedmedia-player-soundthumb' => 'Kei Affspellprojramm',
	'timedmedia-player-selected' => '(Ußjesoht)',
	'timedmedia-use-player' => 'Affspellprojramm:',
	'timedmedia-more' => 'Enshtelle&nbsp;…',
	'timedmedia-dismiss' => 'Zomaache!',
	'timedmedia-download' => 'Datei erunger lade',
	'timedmedia-desc-link' => 'Övver di Datei',
);

/** Latin (Latina)
 * @author SPQRobin
 */
$messages['la'] = array(
	'timedmedia-more' => 'Plus...',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Les Meloures
 * @author Robby
 */
$messages['lb'] = array(
	'timedmedia-desc' => 'Steierungsprogramm fir Ogg Theora a Vorbis Fichieren, mat enger JavaScript-Player-Software',
	'timedmedia-short-audio' => 'Ogg-$1-Tounfichier, $2',
	'timedmedia-short-video' => 'Ogg-$1-Videofichier, $2',
	'timedmedia-short-general' => 'Ogg-$1-Mediefichier, $2',
	'timedmedia-long-audio' => '(tmh-$1-Tounfichier, Dauer: $2, $3)',
	'timedmedia-long-video' => '(Ogg-$1-Videofichier, Dauer: $2, $4×$5 Pixel, $3)',
	'timedmedia-long-multiplexed' => '(Ogg-Toun-/Video-Fichier, $1, Dauer: $2, $4×$5 Pixel, $3)',
	'timedmedia-long-general' => '(Ogg Media-Fichier, Dauer $2, $3)',
	'timedmedia-long-error' => '(Ongëltegen Ogg-Fichier: $1)',
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
	'timedmedia-short-audio' => 'Ogg $1 geluidsbestandj, $2',
	'timedmedia-short-video' => 'Ogg $1 videobestandj, $2',
	'timedmedia-short-general' => 'Ogg $1 mediabestandj, $2',
	'timedmedia-long-audio' => '(Ogg $1 geluidsbestandj, lingdje $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 videobestandj, lingdje $2, $4×$5 pixels, $3)',
	'timedmedia-long-multiplexed' => '(Ogg gemultiplexeerd geluids-/videobestandj, $1, lingdje $2, $4×$5 pixels, $3 totaal)',
	'timedmedia-long-general' => '(Ogg mediabestandj, lingdje $2, $3)',
	'timedmedia-long-error' => '(Óngeljig oggg-bestandj: $1)',
	'timedmedia-play' => 'Aafspele',
	'timedmedia-pause' => 'Óngerbraeke',
	'timedmedia-stop' => 'Oetsjeije',
	'timedmedia-play-video' => 'Video aafspele',
	'timedmedia-play-sound' => 'Geluid aafspele',
	'timedmedia-no-player' => 'Sorry, uch systeem haet gein van de ongersteunde mediaspelers. Installeer estebleef <a href="http://www.java.com/nl/download/manual.jsp">Java</a>.',
	'timedmedia-no-xiphqt' => "'t Liek d'r op det geer 't component XiphQT veur QuickTime neet haet. QuickTime kin timedmedia-bestenj neet aafspele zonger dit component. Download <a href=\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\">XiphQT</a> estebleef of kees 'ne angere speler.",
	'timedmedia-player-videoElement' => 'Native browsersupport',
	'timedmedia-player-oggPlugin' => 'Browserplugin',
	'timedmedia-player-thumbnail' => 'Allein stilstaondj beild',
	'timedmedia-player-soundthumb' => 'Geine mediaspeler',
	'timedmedia-player-selected' => '(geselectieërdj)',
	'timedmedia-use-player' => 'Gebroek speler:',
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
	'timedmedia-short-audio' => 'Ogg $1 garso byla, $2',
	'timedmedia-short-video' => 'Ogg $1 video byla, $2',
	'timedmedia-short-general' => 'Ogg $1 medija byla, $2',
	'timedmedia-long-audio' => '(Ogg $1 garso byla, ilgis $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 video byla, ilgis $2, $4×$5 pikseliai, $3)',
	'timedmedia-long-multiplexed' => '(Ogg sutankinta audio/video byla, $1, ilgis $2, $4×$5 pikseliai, $3 viso)',
	'timedmedia-long-general' => '(Ogg media byla, ilgis $2, $3)',
	'timedmedia-long-error' => '(Bloga ogg byla: $1)',
	'timedmedia-play' => 'Groti',
	'timedmedia-pause' => 'Pauzė',
	'timedmedia-stop' => 'Sustabdyti',
	'timedmedia-play-video' => 'Groti video',
	'timedmedia-play-sound' => 'Groti garsą',
	'timedmedia-no-player' => 'Atsiprašome, neatrodo, kad jūsų sistema turi palaikomą grotuvą. Prašome <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">jį atsisiųsti</a>.',
	'timedmedia-no-xiphqt' => 'Neatrodo, kad jūs turite XiphQT komponentą QuickTime grotuvui. QuickTime negali groti Ogg bylų be šio komponento. Prašome <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">atsisiųsti XiphQT</a> arba pasirinkti kitą grotuvą.',
	'timedmedia-player-videoElement' => 'Pagrindinės naršyklės palaikymas',
	'timedmedia-player-oggPlugin' => 'Naršyklės priedas',
	'timedmedia-player-thumbnail' => 'Tik paveikslėlis',
	'timedmedia-player-soundthumb' => 'Nėra grotuvo',
	'timedmedia-player-selected' => '(pasirinkta)',
	'timedmedia-use-player' => 'Naudoti grotuvą:',
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
	'timedmedia-desc' => 'Обработувач на синхронизирани снимки (видео, аудио, timedText) со транскодирање во Ogg Theora/Vorbis',
	'timedmedia-short-audio' => 'Ogg $1 звучна податотека, $2',
	'timedmedia-short-video' => 'Ogg $1 видео податотека, $2',
	'timedmedia-short-general' => 'Ogg $1 медија податотека, $2',
	'timedmedia-long-audio' => '(Ogg $1 звучна податотека, должина $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 видео податотека, должина $2, $4×$5 пиксели, $3)',
	'timedmedia-long-multiplexed' => '(Ogg мултиплексирана аудио/видео податотека, $1, должина $2, $4×$5 пиксели, $3 вкупно)',
	'timedmedia-long-general' => '(Ogg медија податотека, должина $2, $3)',
	'timedmedia-long-error' => '(Оштетена ogg податотека: $1)',
	'timedmedia-no-player-js' => 'Нажалост, вашиот прелистувач или има оневозможено JavaScript, или нема ниту еден поддржан изведувач.<br />
Можете да го <a href="$1">преземете клипот</a> или <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">да преземете изведувач</a> за да ја пуштите снимката во вашиот прелистувач.',
	'timedmedia-more' => 'Повеќе...',
	'timedmedia-dismiss' => 'Затвори',
	'timedmedia-download' => 'Симни податотека',
	'timedmedia-desc-link' => 'Информации за оваа податотека',
	'timedmedia-oggThumb-version' => 'OggHandler бара oggThumb верзија $1 или понова.',
	'timedmedia-oggThumb-failed' => 'oggThumb не успеа да ја создаде минијатурата.',
);

/** Malayalam (മലയാളം)
 * @author Praveenp
 * @author Shijualex
 */
$messages['ml'] = array(
	'timedmedia-desc' => 'ജാവാസ്ക്രിപ്റ്റ് പ്ലേയർ ഉപയോഗിച്ച് ഓഗ് തിയോറ, വോർബിസ് പ്രമാണങ്ങൾ കൈകാര്യം ചെയ്യൽ',
	'timedmedia-short-audio' => 'ഓഗ് $1 ശബ്ദപ്രമാണം, $2',
	'timedmedia-short-video' => 'ഓഗ് $1 വീഡിയോ പ്രമാണം, $2',
	'timedmedia-short-general' => 'ഓഗ് $1 മീഡിയ പ്രമാണം, $2',
	'timedmedia-long-audio' => '(ഓഗ് $1 ശബ്ദ പ്രമാണം, ദൈർഘ്യം $2, $3)',
	'timedmedia-long-video' => '(ഓഗ് $1 വീഡിയോ പ്രമാണം, ദൈർഘ്യം $2, $4×$5 pixels, $3)',
	'timedmedia-long-multiplexed' => '(ഓഗ് മൾട്ടിപ്ലക്സ്‌‌ഡ് ശബ്ദ/ചലച്ചിത്ര പ്രമാണം, $1, ദൈർഘ്യം $2, $4×$5 ബിന്ദു, ആകെക്കൂടി $3)',
	'timedmedia-long-general' => '(ഓഗ് മീഡിയ പ്രമാണം, ദൈർഘ്യം $2, $3)',
	'timedmedia-long-error' => '(അസാധുവായ ഓഗ് പ്രമാണം: $1)',
	'timedmedia-play' => 'പ്രവർത്തിപ്പിക്കുക',
	'timedmedia-pause' => 'താൽക്കാലികമായി നിർത്തുക',
	'timedmedia-stop' => 'നിർത്തുക',
	'timedmedia-play-video' => 'വീഡിയോ പ്രവർത്തിപ്പിക്കുക',
	'timedmedia-play-sound' => 'ശബ്ദം പ്രവർത്തിപ്പിക്കുക',
	'timedmedia-no-player' => 'ക്ഷമിക്കണം. താങ്കളുടെ കമ്പ്യൂട്ടറിൽ ഓഗ് പ്രമാണം പ്രവർത്തിപ്പിക്കാനാവശ്യമായ സോഫ്റ്റ്‌ഫെയർ ഇല്ല. ദയവു ചെയ്ത് ഒരു പ്ലെയർ <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ഡൗൺലോഡ് ചെയ്യുക</a>.',
	'timedmedia-no-xiphqt' => 'ക്വിക്ക്റ്റൈമിനുള്ള XiphQT ഘടകം താങ്കളുടെ പക്കലുണ്ടെന്നു കാണുന്നില്ല.
ഓഗ് പ്രമാണങ്ങൾ ഈ ഘടകമില്ലാതെ പ്രവർത്തിപ്പിക്കാൻ ക്വിക്ക്റ്റൈമിനു കഴിയില്ല.
ദയവായി <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT ഡൗൺലോഡ് ചെയ്യുക</a> അല്ലെങ്കിൽ മറ്റൊരു പ്ലേയർ തിരഞ്ഞെടുക്കുക.',
	'timedmedia-player-videoElement' => 'ബ്രൗസറിൽ സ്വതവേയുള്ള പിന്തുണ',
	'timedmedia-player-oggPlugin' => 'ബ്രൗസർ പ്ലഗിൻ',
	'timedmedia-player-quicktime-mozilla' => 'ക്വിക്ക്റ്റൈം',
	'timedmedia-player-quicktime-activex' => 'ക്വിക്ക്റ്റൈം (ആക്റ്റീവ്‌‌എക്സ്)',
	'timedmedia-player-thumbnail' => 'നിശ്ചല ചിത്രം മാത്രം',
	'timedmedia-player-soundthumb' => 'പ്ലെയർ ഇല്ല',
	'timedmedia-player-selected' => '(തിരഞ്ഞെടുത്തവ)',
	'timedmedia-use-player' => 'ഈ പ്ലെയർ ഉപയോഗിക്കുക',
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
	'timedmedia-short-audio' => 'ऑग $1 ध्वनी संचिका, $2',
	'timedmedia-short-video' => 'ऑग $1 चलतचित्र संचिका, $2',
	'timedmedia-short-general' => 'ऑग $1 मीडिया संचिका, $2',
	'timedmedia-long-audio' => '(ऑग $1 ध्वनी संचिका, लांबी $2, $3)',
	'timedmedia-long-video' => '(ऑग $1 चलतचित्र संचिका, लांबी $2, $4×$5 पीक्सेल्स, $3)',
	'timedmedia-long-multiplexed' => '(ऑग ध्वनी/चित्र संचिका, $1, लांबी $2, $4×$5 पिक्सेल्स, $3 एकूण)',
	'timedmedia-long-general' => '(ऑग मीडिया संचिका, लांबी $2, $3)',
	'timedmedia-long-error' => '(चुकीची ऑग संचिका: $1)',
	'timedmedia-play' => 'चालू करा',
	'timedmedia-pause' => 'विराम',
	'timedmedia-stop' => 'थांबवा',
	'timedmedia-play-video' => 'चलतचित्र चालू करा',
	'timedmedia-play-sound' => 'ध्वनी चालू करा',
	'timedmedia-no-player' => 'माफ करा, पण तुमच्या संगणकामध्ये कुठलाही प्लेयर आढळला नाही. कृपया <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">प्लेयर डाउनलोड करा</a>.',
	'timedmedia-no-xiphqt' => 'तुमच्या संगणकामध्ये क्वीकटाईम ला लागणारा XiphQT हा तुकडा आढळला नाही. याशिवाय क्वीकटाईम ऑग संचिका चालवू शकणार नाही. कॄपया <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT डाउनलोड करा</a> किंवा दुसरा प्लेयर वापरा.',
	'timedmedia-player-videoElement' => '<video> तुकडा',
	'timedmedia-player-oggPlugin' => 'ऑग प्लगीन',
	'timedmedia-player-cortado' => 'कोर्टाडो (जावा)',
	'timedmedia-player-thumbnail' => 'फक्त स्थिर चित्र',
	'timedmedia-player-soundthumb' => 'प्लेयर उपलब्ध नाही',
	'timedmedia-player-selected' => '(निवडलेले)',
	'timedmedia-use-player' => 'हा प्लेयर वापरा:',
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
	'timedmedia-short-audio' => 'fail bunyi Ogg $1, $2',
	'timedmedia-short-video' => 'fail video Ogg $1, $2',
	'timedmedia-short-general' => 'fail media Ogg $1, $2',
	'timedmedia-long-audio' => '(fail bunyi Ogg $1, tempoh $2, $3)',
	'timedmedia-long-video' => '(fail video Ogg $1, tempoh $2, $4×$5 piksel, $3)',
	'timedmedia-long-multiplexed' => '(fail audio/video multipleks Ogg, $1, tempoh $2, $4×$5 piksel, keseluruhan $3)',
	'timedmedia-long-general' => '(fail media Ogg, tempoh $2, $3)',
	'timedmedia-long-error' => '(Fail Ogg tidak sah: $1)',
	'timedmedia-play' => 'Main',
	'timedmedia-pause' => 'Jeda',
	'timedmedia-stop' => 'Henti',
	'timedmedia-play-video' => 'Main video',
	'timedmedia-play-sound' => 'Main bunyi',
	'timedmedia-no-player' => 'Maaf, sistem anda tidak mempunyai perisian pemain yang disokong. Sila <a href=\\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\\">muat turun sebuah pemain</a>.',
	'timedmedia-no-xiphqt' => 'Anda tidak mempunyai komponen XiphQT untuk QuickTime. QuickTime tidak boleh memainkan fail Ogg tanpa komponen ini. Sila <a href=\\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\\">muat turun XiphQT</a> atau pilih pemain lain.',
	'timedmedia-player-videoElement' => 'Sokongan dalaman pelayar web',
	'timedmedia-player-oggPlugin' => 'Pemalam untuk pelayar web',
	'timedmedia-player-thumbnail' => 'Imej pegun sahaja',
	'timedmedia-player-soundthumb' => 'Tiada pemain',
	'timedmedia-player-selected' => '(dipilih)',
	'timedmedia-use-player' => 'Gunakan pemain:',
	'timedmedia-more' => 'Lagi…',
	'timedmedia-dismiss' => 'Tutup',
	'timedmedia-download' => 'Muat turun fail',
	'timedmedia-desc-link' => 'Perihal fail ini',
);

/** Erzya (Эрзянь)
 * @author Botuzhaleny-sodamo
 */
$messages['myv'] = array(
	'timedmedia-play' => 'Седик',
	'timedmedia-pause' => 'Аштевтик',
	'timedmedia-stop' => 'Лоткавтык',
	'timedmedia-play-video' => 'Нолдык видеонть',
	'timedmedia-play-sound' => 'Нолдык вайгеленть',
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
	'timedmedia-short-audio' => 'Ogg-$1-Toondatei, $2',
	'timedmedia-short-video' => 'Ogg-$1-Videodatei, $2',
	'timedmedia-short-general' => 'Ogg-$1-Mediendatei, $2',
	'timedmedia-long-audio' => '(Ogg-$1-Toondatei, $2 lang, $3)',
	'timedmedia-long-video' => '(Ogg-$1-Videodatei, $2 lang, $4×$5 Pixels, $3)',
	'timedmedia-long-multiplexed' => '(Ogg-Multiplexed-Audio-/Video-Datei, $1, $2 lang, $4×$5 Pixels, $3 alltohoop)',
	'timedmedia-long-general' => '(Ogg-Mediendatei, $2 lang, $3)',
	'timedmedia-long-error' => '(Kaputte Ogg-Datei: $1)',
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
	'timedmedia-short-audio' => 'Ogg $1 geluudsbestaand, $2',
	'timedmedia-short-video' => 'Ogg $1 videobestaand, $2',
	'timedmedia-short-general' => 'Ogg $1 mediabestaand, $2',
	'timedmedia-long-audio' => '(Ogg $1 geluudsbestaand, lengte $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 videobestaand, lengte $2, $4×$5 pixels, $3)',
	'timedmedia-long-multiplexed' => '(Ogg emultiplexed geluuds-/videobestaand, $1, lengte $2, $4×$5 pixels, $3 totaal)',
	'timedmedia-long-general' => '(Ogg-mediabestaand, lengte $2, $3)',
	'timedmedia-long-error' => '(Ongeldig Ogg-bestaand: $1)',
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
	'timedmedia-desc' => 'Handelt Timed Media af (video, audio timedText) en codeert naar Ogg Theora/Vorbis',
	'timedmedia-short-audio' => 'Ogg $1 geluidsbestand, $2',
	'timedmedia-short-video' => 'Ogg $1 videobestand, $2',
	'timedmedia-short-general' => 'Ogg $1 mediabestand, $2',
	'timedmedia-long-audio' => '(Ogg $1 geluidsbestand, lengte $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 video file, lengte $2, $4×$5 pixels, $3)',
	'timedmedia-long-multiplexed' => '(Ogg gemultiplexed geluids/videobestand, $1, lengte $2, $4×$5 pixels, $3 totaal)',
	'timedmedia-long-general' => '(Ogg mediabestand, lengte $2, $3)',
	'timedmedia-long-error' => '(Ongeldig Ogg-bestand: $1)',
	'timedmedia-no-player-js' => 'Uw systeem heeft JavaScript uitgeschakeld of er is geen ondersteunde mediaspeler.<br />
U kunt <a href="$1">de clip downloaden</a> of <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">een mediaspeler downloaden</a> om de clip af te spelen in uw browser.',
	'timedmedia-more' => 'Meer…',
	'timedmedia-dismiss' => 'Sluiten',
	'timedmedia-download' => 'Bestand downloaden',
	'timedmedia-desc-link' => 'Over dit bestand',
	'timedmedia-oggThumb-version' => 'OggHandler vereist oggThumb versie $1 of hoger.',
	'timedmedia-oggThumb-failed' => 'oggThumb kon geen miniatuur aanmaken.',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Eirik
 * @author Harald Khan
 */
$messages['nn'] = array(
	'timedmedia-desc' => 'Gjer at Ogg Theora- og Ogg Vorbis-filer kan verta køyrte ved hjelp av JavaScript-avspelar.',
	'timedmedia-short-audio' => 'Ogg $1-lydfil, $2',
	'timedmedia-short-video' => 'Ogg $1-videofil, $2',
	'timedmedia-short-general' => 'Ogg $1-mediafil, $2',
	'timedmedia-long-audio' => '(Ogg $1-lydfil, lengd $2, $3)',
	'timedmedia-long-video' => '(Ogg $1-videofil, lengd $2, $4×$5 pikslar, $3)',
	'timedmedia-long-multiplexed' => '(Samansett ogg lyd-/videofil, $1, lengd $2, $4×$5 pikslar, $3 til saman)',
	'timedmedia-long-general' => '(Ogg mediafil, lengd $2, $3)',
	'timedmedia-long-error' => '(Ugyldig timedmedia-fil: $1)',
	'timedmedia-play' => 'Spel av',
	'timedmedia-pause' => 'Pause',
	'timedmedia-stop' => 'Stopp',
	'timedmedia-play-video' => 'Spel av videofila',
	'timedmedia-play-sound' => 'Spel av lydfila',
	'timedmedia-no-player' => 'Beklagar, systemet ditt har ikkje støtta programvare til avspeling. Ver venleg og <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">last ned ein avspelar</a>.',
	'timedmedia-no-xiphqt' => 'Du ser ikkje ut til å ha XiphQT-komponenten til QuickTime. QuickTime kan ikkje spele av timedmedia-filer utan denne. Ver venleg og <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">last ned XiphQT</a> eller vel ein annan avspelar.',
	'timedmedia-player-videoElement' => 'Innebygd nettlesarstøtte',
	'timedmedia-player-oggPlugin' => 'Programtillegg for nettlesar',
	'timedmedia-player-thumbnail' => 'Berre stillbilete',
	'timedmedia-player-soundthumb' => 'Ingen avspelar',
	'timedmedia-player-selected' => '(valt)',
	'timedmedia-use-player' => 'Bruk avspelaren:',
	'timedmedia-more' => 'Meir...',
	'timedmedia-dismiss' => 'Lat att',
	'timedmedia-download' => 'Last ned fila',
	'timedmedia-desc-link' => 'Om denne fila',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 * @author Laaknor
 */
$messages['no'] = array(
	'timedmedia-desc' => 'Gjør at Ogg Theora- og Ogg Vorbis-filer kan kjøres med hjelp av JavaScript-avspiller.',
	'timedmedia-short-audio' => 'Ogg $1 lydfil, $2',
	'timedmedia-short-video' => 'Ogg $1 videofil, $2',
	'timedmedia-short-general' => 'Ogg $1 mediefil, $2',
	'timedmedia-long-audio' => '(Ogg $1 lydfil, lengde $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 videofil, lengde $2, $4×$5 piksler, $3)',
	'timedmedia-long-multiplexed' => '(Sammensatt ogg lyd-/videofil, $1, lengde $2, $4×$5 piksler, $3 til sammen)',
	'timedmedia-long-general' => '(Ogg mediefil, lengde $2, $3)',
	'timedmedia-long-error' => '(Ugyldig timedmedia-fil: $1)',
	'timedmedia-play' => 'Spill',
	'timedmedia-pause' => 'Pause',
	'timedmedia-stop' => 'Stopp',
	'timedmedia-play-video' => 'Spill av video',
	'timedmedia-play-sound' => 'Spill av lyd',
	'timedmedia-no-player' => 'Beklager, systemet ditt har ingen medieavspillere som støtter filformatet. Vennligst <a href="http://mediawiki.org/wiki/Extension:OggHandler/Client_download">last ned en avspiller</a> som støtter formatet.',
	'timedmedia-no-xiphqt' => 'Du har ingen XiphQT-komponent for QuickTime. QuickTime kan ikke spille timedmedia-filer uten denne komponenten. <a href="http://mediawiki.org/wiki/Extension:OggHandler/Client_download">last ned XiphQT</a> eller velg en annen medieavspiller.',
	'timedmedia-player-videoElement' => 'Innebygd nettleserstøtte',
	'timedmedia-player-oggPlugin' => 'Programtillegg for nettleser',
	'timedmedia-player-thumbnail' => 'Kun stillbilder',
	'timedmedia-player-soundthumb' => 'Ingen medieavspiller',
	'timedmedia-player-selected' => '(valgt)',
	'timedmedia-use-player' => 'Bruk avspiller:',
	'timedmedia-more' => 'Mer …',
	'timedmedia-dismiss' => 'Lukk',
	'timedmedia-download' => 'Last ned fil',
	'timedmedia-desc-link' => 'Om denne filen',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'timedmedia-desc' => 'Supòrt pels fichièrs Ogg Theora e Vorbis, amb un lector Javascript',
	'timedmedia-short-audio' => 'Fichièr son Ogg $1, $2',
	'timedmedia-short-video' => 'Fichièr vidèo Ogg $1, $2',
	'timedmedia-short-general' => 'Fichièr mèdia Ogg $1, $2',
	'timedmedia-long-audio' => '(Fichièr son Ogg $1, durada $2, $3)',
	'timedmedia-long-video' => '(Fichièr vidèo Ogg $1, durada $2, $4×$5 pixèls, $3)',
	'timedmedia-long-multiplexed' => '(Fichièr multiplexat àudio/vidèo Ogg, $1, durada $2, $4×$5 pixèls, $3)',
	'timedmedia-long-general' => '(Fichièr mèdia Ogg, durada $2, $3)',
	'timedmedia-long-error' => '(Fichièr Ogg invalid : $1)',
	'timedmedia-play' => 'Legir',
	'timedmedia-pause' => 'Pausa',
	'timedmedia-stop' => 'Stòp',
	'timedmedia-play-video' => 'Legir la vidèo',
	'timedmedia-play-sound' => 'Legir lo son',
	'timedmedia-no-player' => 'O planhèm, aparentament, vòstre sistèma a pas cap de lectors suportats. Installatz <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/oc">un dels lectors suportats</a>.',
	'timedmedia-no-xiphqt' => 'Aparentament avètz pas lo compausant XiphQT per Quicktime. Quicktime pòt pas legir los fiquièrs Ogg sens aqueste compausant. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/fr"> Telecargatz-lo XiphQT</a> o causissètz un autre lector.',
	'timedmedia-player-videoElement' => 'Supòrt del navigador natiu',
	'timedmedia-player-oggPlugin' => 'Plugin del navigador',
	'timedmedia-player-thumbnail' => 'Imatge estatic solament',
	'timedmedia-player-soundthumb' => 'Cap de lector',
	'timedmedia-player-selected' => '(seleccionat)',
	'timedmedia-use-player' => 'Utilizar lo lector :',
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
	'timedmedia-desc' => 'Obsługa plików w formacie Ogg Theora i Vorbis z odtwarzaczem w JavaScripcie',
	'timedmedia-short-audio' => 'Plik dźwiękowy Ogg $1, $2',
	'timedmedia-short-video' => 'Plik wideo Ogg $1, $2',
	'timedmedia-short-general' => 'Plik multimedialny Ogg $1, $2',
	'timedmedia-long-audio' => '(plik dźwiękowy Ogg $1, długość $2, $3)',
	'timedmedia-long-video' => '(plik wideo Ogg $1, długość $2, rozdzielczość $4×$5, $3)',
	'timedmedia-long-multiplexed' => '(plik audio/wideo Ogg, $1, długość $2, rozdzielczość $4×$5, ogółem $3)',
	'timedmedia-long-general' => '(plik multimedialny Ogg, długość $2, $3)',
	'timedmedia-long-error' => '(niepoprawny plik Ogg: $1)',
	'timedmedia-play' => 'Odtwórz',
	'timedmedia-pause' => 'Pauza',
	'timedmedia-stop' => 'Stop',
	'timedmedia-play-video' => 'Odtwórz wideo',
	'timedmedia-play-sound' => 'Odtwórz dźwięk',
	'timedmedia-no-player' => 'W Twoim systemie brak obsługiwanego programu odtwarzacza. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/pl">Pobierz i zainstaluj odtwarzacz</a>.',
	'timedmedia-no-xiphqt' => 'Brak komponentu XiphQT dla programu QuickTime. QuickTime nie może odtwarzać plików Ogg bez tego komponentu. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/pl">Pobierz XiphQT</a> lub użyj innego odtwarzacza.',
	'timedmedia-player-videoElement' => 'Obsługa bezpośrednio przez przeglądarkę',
	'timedmedia-player-oggPlugin' => 'Wtyczka do przeglądarki',
	'timedmedia-player-thumbnail' => 'Tylko nieruchomy obraz',
	'timedmedia-player-soundthumb' => 'Bez odtwarzacza',
	'timedmedia-player-selected' => '(wybrany)',
	'timedmedia-use-player' => 'Użyj odtwarzacza:',
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
	'timedmedia-short-audio' => 'Registrassion Ogg $1, $2',
	'timedmedia-short-video' => 'Film Ogg $1, $2',
	'timedmedia-short-general' => 'Archivi Multimojen Ogg $1, $2',
	'timedmedia-long-audio' => "(Registrassion Ogg $1, ch'a dura $2, $3)",
	'timedmedia-long-video' => "(Film Ogg $1, ch'a dura $2, formà $4×$5 px, $3)",
	'timedmedia-long-multiplexed' => "(Archivi audio/video multiplessà Ogg, $1, ch'a dura $2, formà $4×$5 px, $3 an tut)",
	'timedmedia-long-general' => "(Archivi multimojen Ogg, ch'a dura $2, $3)",
	'timedmedia-long-error' => '(Archivi ogg nen bon: $1)',
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
	'timedmedia-short-audio' => 'Ogg $1 غږيزه دوتنه، $2',
	'timedmedia-short-video' => 'Ogg $1 ويډيويي دوتنه، $2',
	'timedmedia-short-general' => 'Ogg $1 رسنيزه دوتنه، $2',
	'timedmedia-play' => 'غږول',
	'timedmedia-stop' => 'درول',
	'timedmedia-play-video' => 'ويډيو غږول',
	'timedmedia-play-sound' => 'غږ غږول',
	'timedmedia-player-videoElement' => 'د کورني کتنمل ملاتړ',
	'timedmedia-player-thumbnail' => 'يوازې ولاړ انځور',
	'timedmedia-player-soundthumb' => 'هېڅ کوم غږونکی نه',
	'timedmedia-player-selected' => '(ټاکل شوی)',
	'timedmedia-use-player' => 'غږونکی کارول:',
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
	'timedmedia-desc' => 'Manuseador para ficheiros Ogg Theora e Vorbis, com reprodutor JavaScript',
	'timedmedia-short-audio' => 'Áudio Ogg $1, $2',
	'timedmedia-short-video' => 'Vídeo Ogg $1, $2',
	'timedmedia-short-general' => 'Multimédia Ogg $1, $2',
	'timedmedia-long-audio' => '(Áudio Ogg $1, $2 de duração, $3)',
	'timedmedia-long-video' => '(Vídeo Ogg $1, $2 de duração, $4×$5 pixels, $3)',
	'timedmedia-long-multiplexed' => '(Áudio/vídeo Ogg multifacetado, $1, $2 de duração, $4×$5 pixels, $3 no todo)',
	'timedmedia-long-general' => '(Multimédia Ogg, $2 de duração, $3)',
	'timedmedia-long-error' => '(Ficheiro ogg inválido: $1)',
	'timedmedia-no-player-js' => 'Desculpe, mas ou o seu browser está com o JavaScript desactivado ou não tem qualquer leitor  suportado.<br />
Pode fazer o <a href="$1">download do vídeo</a> ou o <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">download de um leitor</a> para assistir ao vídeo no seu browser.',
	'timedmedia-more' => 'Mais...',
	'timedmedia-dismiss' => 'Fechar',
	'timedmedia-download' => 'Fazer download do ficheiro',
	'timedmedia-desc-link' => 'Sobre este ficheiro',
	'timedmedia-oggThumb-version' => 'O oggHandler requer o oggThumb versão $1 ou posterior.',
	'timedmedia-oggThumb-failed' => 'O oggThumb não conseguiu criar a miniatura.',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Eduardo.mps
 * @author Giro720
 */
$messages['pt-br'] = array(
	'timedmedia-desc' => 'Manipulador para arquivos Ogg Theora e Vorbis, com reprodutor JavaScript',
	'timedmedia-short-audio' => 'Arquivo de áudio Ogg $1, $2',
	'timedmedia-short-video' => 'Arquivo de vídeo Ogg $1, $2',
	'timedmedia-short-general' => 'Arquivo multimídia Ogg $1, $2',
	'timedmedia-long-audio' => '(Arquivo de Áudio Ogg $1, $2 de duração, $3)',
	'timedmedia-long-video' => '(Vídeo Ogg $1, $2 de duração, $4×$5 pixels, $3)',
	'timedmedia-long-multiplexed' => '(Áudio/vídeo Ogg multifacetado, $1, $2 de duração, $4×$5 pixels, $3 no todo)',
	'timedmedia-long-general' => '(Multimídia Ogg, $2 de duração, $3)',
	'timedmedia-long-error' => '(Ficheiro ogg inválido: $1)',
	'timedmedia-no-player-js' => 'Desculpe, seu navegador ou está com JavaScript desabilitado ou não tem nenhum "player" suportado.<br />
Você pode <a href="$1">descarregar o clipe</a> ou <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">descarregar um "player"</a> para executar o clipe em seu navegador.',
	'timedmedia-more' => 'Mais...',
	'timedmedia-dismiss' => 'Fechar',
	'timedmedia-download' => 'Descarregar arquivo',
	'timedmedia-desc-link' => 'Sobre este arquivo',
);

/** Quechua (Runa Simi)
 * @author AlimanRuna
 */
$messages['qu'] = array(
	'timedmedia-play' => 'Waqachiy',
	'timedmedia-pause' => "P'itiy",
	'timedmedia-stop' => 'Tukuchiy',
	'timedmedia-play-video' => 'Widyuta rikuchiy',
	'timedmedia-play-sound' => 'Ruqyayta uyarichiy',
	'timedmedia-player-soundthumb' => 'Manam waqachiqchu',
	'timedmedia-player-selected' => '(akllasqa)',
	'timedmedia-use-player' => "Kay waqachiqta llamk'achiy:",
	'timedmedia-more' => 'Astawan...',
	'timedmedia-dismiss' => "Wichq'ay",
	'timedmedia-download' => 'Willañiqita chaqnamuy',
	'timedmedia-desc-link' => 'Kay willañiqimanta',
);

/** Romanian (Română)
 * @author KlaudiuMihaila
 * @author Mihai
 * @author Stelistcristi
 */
$messages['ro'] = array(
	'timedmedia-short-audio' => 'Fișier de sunet ogg $1, $2',
	'timedmedia-short-video' => 'Fișier video ogg $1, $2',
	'timedmedia-short-general' => 'Fișier media ogg $1, $2',
	'timedmedia-long-audio' => '(Fișier de sunet ogg $1, lungime $2, $3)',
	'timedmedia-long-video' => '(Fișier video ogg $1, lungime $2, $4×$5 pixeli, $3)',
	'timedmedia-long-multiplexed' => '(Fișier multiplexat audio/video ogg, $1, lungime $2, $4×$5 pixeli, $3)',
	'timedmedia-long-general' => '(Fișier media ogg, lungime $2, $3)',
	'timedmedia-long-error' => '(Fișier ogg incorect: $1)',
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
	'timedmedia-short-audio' => 'File audie Ogg $1, $2',
	'timedmedia-short-video' => 'File video Ogg $1, $2',
	'timedmedia-short-general' => 'File media Ogg $1, $2',
	'timedmedia-long-audio' => '(File audie Ogg $1, lunghezze $2, $3)',
	'timedmedia-long-video' => '(File video Ogg $1, lunghezze $2, $4 x $5 pixel, $3)',
	'timedmedia-long-multiplexed' => '(File multiplexed audie e video Ogg $1, lunghezze $2, $4 x $5 pixel, $3 in totale)',
	'timedmedia-long-general' => '(File media Ogg, lunghezze $2, $3)',
	'timedmedia-long-error' => '(Ogg file invalide: $1)',
	'timedmedia-play' => 'Riproduce',
	'timedmedia-pause' => 'Mitte in pause',
	'timedmedia-stop' => 'Stuèppe',
	'timedmedia-play-video' => "Riproduce 'u video",
	'timedmedia-play-sound' => 'Riproduce le suène',
	'timedmedia-no-player' => "Ne dispiace, 'u sisteme tune pare ca non ge tène nisciune softuare p'a riproduzione.<br />
Pe piacere, <a href=\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\">scareche 'u reproduttore</a>.",
	'timedmedia-no-xiphqt' => "Non ge pare ca tìne 'u combonende XiphQT pu QuickTime.<br />
QuickTime non ge pò reproducere file Ogg senze stu combonende.<br />
Pe piacere <a href=\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\">scareche XiphQT</a> o scacchie 'n'otre reproduttore.",
	'timedmedia-player-videoElement' => 'Supporte browser native',
	'timedmedia-player-oggPlugin' => "Plugin d'u browser",
	'timedmedia-player-thumbnail' => 'Angore sulamende immaggine',
	'timedmedia-player-soundthumb' => 'Nisciune reproduttore',
	'timedmedia-player-selected' => '(scacchiate)',
	'timedmedia-use-player' => "Ause 'u reproduttore:",
	'timedmedia-more' => 'De cchiù...',
	'timedmedia-dismiss' => 'Chiude',
	'timedmedia-download' => 'Scareche stu file',
	'timedmedia-desc-link' => "'Mbormaziune sus a stu file",
);

/** Russian (Русский)
 * @author Ahonc
 * @author Kv75
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'timedmedia-desc' => 'Обработчик файлов Ogg Theora и Vorbis с использованием JavaScript-проигрывателя',
	'timedmedia-short-audio' => 'Звуковой файл Ogg $1, $2',
	'timedmedia-short-video' => 'Видео-файл Ogg $1, $2',
	'timedmedia-short-general' => 'Медиа-файл Ogg $1, $2',
	'timedmedia-long-audio' => '(звуковой файл Ogg $1, длина $2, $3)',
	'timedmedia-long-video' => '(видео-файл Ogg $1, длина $2, $4×$5 пикселов, $3)',
	'timedmedia-long-multiplexed' => '(мультиплексный аудио/видео-файл Ogg, $1, длина $2, $4×$5 пикселов, $3 всего)',
	'timedmedia-long-general' => '(медиа-файл Ogg, длина $2, $3)',
	'timedmedia-long-error' => '(неправильный Ogg-файл: $1)',
	'timedmedia-no-player-js' => 'К сожалению, в вашем браузере отключён JavaScript, или не имеется требуемого проигрывателя.<br />
Вы можете <a href="$1">загрузить ролик</a> или <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">загрузить проигрыватель</a> для воспроизведения ролика в браузере.',
	'timedmedia-more' => 'Больше…',
	'timedmedia-dismiss' => 'Скрыть',
	'timedmedia-download' => 'Загрузить файл',
	'timedmedia-desc-link' => 'Информация об этом файле',
	'timedmedia-oggThumb-version' => 'OggHandler требует oggThumb версии $1 или более поздней.',
	'timedmedia-oggThumb-failed' => 'oggThumb не удалось создать миниатюру.',
);

/** Yakut (Саха тыла)
 * @author HalanTul
 */
$messages['sah'] = array(
	'timedmedia-desc' => 'Обработчик файлов Ogg Theora и Vorbis с использованием JavaScript-проигрывателя',
	'timedmedia-short-audio' => 'Звуковой файл Ogg $1, $2',
	'timedmedia-short-video' => 'Видео-файл Ogg $1, $2',
	'timedmedia-short-general' => 'Медиа-файл Ogg $1, $2',
	'timedmedia-long-audio' => '(звуковой файл Ogg $1, уһуна $2, $3)',
	'timedmedia-long-video' => '(видео-файл Ogg $1, уһуна $2, $4×$5 пииксэллээх, $3)',
	'timedmedia-long-multiplexed' => '(мультиплексный аудио/видео-файл Ogg, $1, уһуна $2, $4×$5 пииксэллээх, барыта $3)',
	'timedmedia-long-general' => '(медиа-файл Ogg, уһуна $2, $3)',
	'timedmedia-long-error' => '(сыыһа timedmedia-файл: $1)',
	'timedmedia-play' => 'Оонньот',
	'timedmedia-pause' => 'Тохтото түс',
	'timedmedia-stop' => 'Тохтот',
	'timedmedia-play-video' => 'Көрдөр',
	'timedmedia-play-sound' => 'Иһитиннэр',
	'timedmedia-no-player' => 'Хомойуох иһин эн систиэмэҕэр иһитиннэрэр/көрдөрөр анал бырагырааммалар суохтар эбит. Бука диэн, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">плееры хачайдан</a>.',
	'timedmedia-no-xiphqt' => 'QuickTime маннык тэрээбэтэ: XiphQT суох эбит. Онон QuickTime бу Ogg билэни (файлы) оонньотор кыаҕа суох. Бука диэн, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download"> XiphQT хачайдан</a> эбэтэр атын плееры тал.',
	'timedmedia-player-videoElement' => 'Браузер бэйэтин өйөөһүнэ',
	'timedmedia-player-oggPlugin' => 'Браузер плагина',
	'timedmedia-player-thumbnail' => 'Хамсаабат ойууну эрэ',
	'timedmedia-player-soundthumb' => 'Плеер суох',
	'timedmedia-player-selected' => '(талыллыбыт)',
	'timedmedia-use-player' => 'Бу плееры туттарга:',
	'timedmedia-more' => 'Өссө...',
	'timedmedia-dismiss' => 'Кистээ/сап',
	'timedmedia-download' => 'Билэни хачайдаа',
	'timedmedia-desc-link' => 'Бу билэ туһунан',
);

/** Sinhala (සිංහල)
 * @author නන්දිමිතුරු
 */
$messages['si'] = array(
	'timedmedia-desc' => 'Ogg Theora සහ Vorbis ගොනු සඳහා හසුරුවනය, ජාවාස්ක්‍රිප්ට් ප්ලේයර් සමඟ',
	'timedmedia-short-audio' => 'Ogg $1 ශ්‍රව්‍ය ගොනුව, $2',
	'timedmedia-short-video' => 'Ogg $1 දෘශ්‍ය ගොනුව, $2',
	'timedmedia-short-general' => 'Ogg $1 මාධ්‍ය ගොනුව, $2',
	'timedmedia-long-audio' => '(Ogg $1 ශ්‍රව්‍ය ගොනුව, ප්‍රවර්තනය $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 දෘශ්‍ය ගොනුව, ප්‍රවර්තනය $2, $4×$5 පික්සල්, $3)',
	'timedmedia-long-multiplexed' => '(Ogg බහුපථකාරක ශ්‍රව්‍ය/දෘශ්‍ය ගොනුව, $1, ප්‍රවර්තනය $2, $4×$5 පික්සල්, $3 සමස්ත)',
	'timedmedia-long-general' => '(Ogg මාධ්‍ය ගොනුව, ප්‍රවර්තනය $2, $3)',
	'timedmedia-long-error' => '(අනීතික ogg ගොනුව: $1)',
	'timedmedia-play' => 'වාදනය කරන්න',
	'timedmedia-pause' => 'විරාම කරන්න',
	'timedmedia-stop' => 'නවතන්න',
	'timedmedia-play-video' => 'දෘශ්‍ය වාදනය කරන්න',
	'timedmedia-play-sound' => 'ශබ්දය වාදනය කරන්න',
	'timedmedia-no-player' => 'කණගාටුයි, කිසිම සහායක ධාවක මෘදුකාංගයක් ඔබ පද්ධතිය සතුව ඇති බවක් නොපෙනේ.
කරුණාකර <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ධාවකයක් බා ගන්න</a>.',
	'timedmedia-no-xiphqt' => 'QuickTime සඳහා XiphQT සංරචකය ඔබ සතුව ඇති බවක් නොපෙනේ.
මෙම සංරචකය නොමැතිව Ogg ගොනු ධාවනය කිරීම  QuickTime විසින් සිදුකල නොහැක.
කරුණාකර <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download"> XiphQT බා ගන්න</a> නැතහොත් වෙනත් ධාවකයක් තෝරාගන්න.',
	'timedmedia-player-oggPlugin' => 'බ්‍රවුසර ප්ලගිත',
	'timedmedia-player-cortado' => 'Cortado (ජාවා)',
	'timedmedia-player-thumbnail' => 'නිශ්චල රූප පමණි',
	'timedmedia-player-soundthumb' => 'ධාවකයක් නොමැත',
	'timedmedia-player-selected' => '(තෝරාගෙන)',
	'timedmedia-use-player' => 'ධාවකය භාවිතා කරන්න:',
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
	'timedmedia-short-audio' => 'Zvukový súbor ogg $1, $2',
	'timedmedia-short-video' => 'Video súbor ogg $1, $2',
	'timedmedia-short-general' => 'Multimediálny súbor ogg $1, $2',
	'timedmedia-long-audio' => '(Zvukový súbor ogg $1, dĺžka $2, $3)',
	'timedmedia-long-video' => '(Video súbor ogg $1, dĺžka $2, $4×$5 pixelov, $3)',
	'timedmedia-long-multiplexed' => '(Multiplexovaný zvukový/video súbor ogg, $1, dĺžka $2, $4×$5 pixelov, $3 celkom)',
	'timedmedia-long-general' => '(Multimediálny súbor ogg, dĺžka $2, $3)',
	'timedmedia-long-error' => '(Neplatný súbor ogg: $1)',
	'timedmedia-play' => 'Prehrať',
	'timedmedia-pause' => 'Pozastaviť',
	'timedmedia-stop' => 'Zastaviť',
	'timedmedia-play-video' => 'Prehrať video',
	'timedmedia-play-sound' => 'Prehrať zvuk',
	'timedmedia-no-player' => 'Prepáčte, zdá sa, že váš systém nemá žiadny podporovaný softvér na prehrávanie. Prosím, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">stiahnite si prehrávač</a>.',
	'timedmedia-no-xiphqt' => 'Zdá sa, že nemáte komponent QuickTime XiphQT. QuickTime nedokáže prehrávať ogg súbory bez tohto komponentu. Prosím, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">stiahnite si XiphQT</a> alebo si vyberte iný prehrávač.',
	'timedmedia-player-videoElement' => 'Natívna podpora prehliadača',
	'timedmedia-player-oggPlugin' => 'Zásuvný modul prehliadača',
	'timedmedia-player-thumbnail' => 'iba nepohyblivý obraz',
	'timedmedia-player-soundthumb' => 'žiadny prehrávač',
	'timedmedia-player-selected' => '(vybraný)',
	'timedmedia-use-player' => 'Použiť prehrávač:',
	'timedmedia-more' => 'viac...',
	'timedmedia-dismiss' => 'Zatvoriť',
	'timedmedia-download' => 'Stiahnuť súbor',
	'timedmedia-desc-link' => 'O tomto súbore',
);

/** Slovenian (Slovenščina)
 * @author Dbc334
 */
$messages['sl'] = array(
	'timedmedia-play' => 'Predvajaj',
	'timedmedia-pause' => 'Pavza',
	'timedmedia-stop' => 'Ustavi',
	'timedmedia-play-video' => 'Predvajaj video',
	'timedmedia-play-sound' => 'Predvajaj zvok',
	'timedmedia-player-videoElement' => 'Vgrajena podpora brskalnika',
	'timedmedia-player-thumbnail' => 'Samo stoječa slika',
	'timedmedia-player-soundthumb' => 'Brez predvajalnika',
	'timedmedia-player-selected' => '(izbrano)',
	'timedmedia-use-player' => 'Uporabi predvajalnik:',
	'timedmedia-more' => 'Več ...',
	'timedmedia-dismiss' => 'Zapri',
	'timedmedia-download' => 'Prenesi datoteko',
	'timedmedia-desc-link' => 'O datoteki',
);

/** Albanian (Shqip)
 * @author Dori
 */
$messages['sq'] = array(
	'timedmedia-short-audio' => 'Skedë zanore Ogg $1, $2',
	'timedmedia-short-video' => 'Skedë pamore Ogg $1, $2',
	'timedmedia-short-general' => 'Skedë mediatike Ogg $1, $2',
	'timedmedia-long-audio' => '(Skedë zanore Ogg $1, kohëzgjatja $2, $3)',
	'timedmedia-long-video' => '(Skedë pamore Ogg $1, kohëzgjatja $2, $4×$5 pixel, $3)',
	'timedmedia-play' => 'Fillo',
	'timedmedia-pause' => 'Pusho',
	'timedmedia-stop' => 'Ndalo',
	'timedmedia-play-video' => 'Fillo videon',
	'timedmedia-play-sound' => 'Fillo zërin',
	'timedmedia-no-player' => 'Ju kërkojmë ndjesë por sistemi juaj nuk ka mundësi për të kryer këtë veprim. Mund të <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">shkarkoni një mjet</a> tjetër.',
	'timedmedia-more' => 'Më shumë...',
	'timedmedia-dismiss' => 'Mbylle',
	'timedmedia-download' => 'Shkarko skedën',
	'timedmedia-desc-link' => 'Rreth kësaj skede',
);

/** Serbian Cyrillic ekavian (Српски (ћирилица))
 * @author Millosh
 * @author Sasa Stefanovic
 * @author Михајло Анђелковић
 */
$messages['sr-ec'] = array(
	'timedmedia-desc' => 'Руковаоц ogg Теора и Ворбис фајловима са јаваскрипт плејером',
	'timedmedia-short-audio' => 'Ogg $1 звучни фајл, $2.',
	'timedmedia-short-video' => 'Ogg $1 видео фајл, $2.',
	'timedmedia-short-general' => 'Ogg $1 медијски фајл, $2.',
	'timedmedia-long-audio' => '(Ogg $1 звучни фајл, дужина $2, $3.)',
	'timedmedia-long-video' => '(Ogg $1 видео фајл, дужина $2, $4×$5 пиксела, $3.)',
	'timedmedia-long-multiplexed' => '(Ogg мултиплексовани аудио/видео фајл, $1, дужина $2, $4×$5 пиксела, $3 укупно.)',
	'timedmedia-long-general' => '(Ogg медијски фајл, дужина $2, $3.)',
	'timedmedia-long-error' => '(Лош ogg фајл: $1.)',
	'timedmedia-play' => 'Пусти',
	'timedmedia-pause' => 'Пауза',
	'timedmedia-stop' => 'Стоп',
	'timedmedia-play-video' => 'Пусти видео',
	'timedmedia-play-sound' => 'Пусти звук',
	'timedmedia-player-videoElement' => 'Уграђена подршка у браузер',
	'timedmedia-player-oggPlugin' => 'Плагин за браузер',
	'timedmedia-player-thumbnail' => 'још увек само слика',
	'timedmedia-player-soundthumb' => 'нема плејера',
	'timedmedia-player-selected' => '(означено)',
	'timedmedia-use-player' => 'Користи плејер:',
	'timedmedia-more' => 'Више...',
	'timedmedia-dismiss' => 'Затвори',
	'timedmedia-download' => 'Преузми фајл',
	'timedmedia-desc-link' => 'О овом фајлу',
);

/** Serbian Latin ekavian (Srpski (latinica))
 * @author Michaello
 */
$messages['sr-el'] = array(
	'timedmedia-desc' => 'Rukovaoc ogg Teora i Vorbis fajlovima sa javaskript plejerom',
	'timedmedia-short-audio' => 'Ogg $1 zvučni fajl, $2.',
	'timedmedia-short-video' => 'Ogg $1 video fajl, $2.',
	'timedmedia-short-general' => 'Ogg $1 medijski fajl, $2.',
	'timedmedia-long-audio' => '(Ogg $1 zvučni fajl, dužina $2, $3.)',
	'timedmedia-long-video' => '(Ogg $1 video fajl, dužina $2, $4×$5 piksela, $3.)',
	'timedmedia-long-multiplexed' => '(Ogg multipleksovani audio/video fajl, $1, dužina $2, $4×$5 piksela, $3 ukupno.)',
	'timedmedia-long-general' => '(Ogg medijski fajl, dužina $2, $3.)',
	'timedmedia-long-error' => '(Loš ogg fajl: $1.)',
	'timedmedia-play' => 'Pusti',
	'timedmedia-pause' => 'Pauza',
	'timedmedia-stop' => 'Stop',
	'timedmedia-play-video' => 'Pusti video',
	'timedmedia-play-sound' => 'Pusti zvuk',
	'timedmedia-player-videoElement' => 'Ugrađena podrška u brauzer',
	'timedmedia-player-oggPlugin' => 'Plagin za brauzer',
	'timedmedia-player-thumbnail' => 'još uvek samo slika',
	'timedmedia-player-soundthumb' => 'nema plejera',
	'timedmedia-player-selected' => '(označeno)',
	'timedmedia-use-player' => 'Koristi plejer:',
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
	'timedmedia-short-audio' => 'Ogg-$1-Audiodoatäi, $2',
	'timedmedia-short-video' => 'Ogg-$1-Videodoatäi, $2',
	'timedmedia-short-general' => 'Ogg-$1-Mediadoatäi, $2',
	'timedmedia-long-audio' => '(Ogg-$1-Audiodoatäi, Loangte: $2, $3)',
	'timedmedia-long-video' => '(Ogg-$1-Videodoatäi, Loangte: $2, $4×$5 Pixel, $3)',
	'timedmedia-long-multiplexed' => '(Ogg-Audio-/Video-Doatäi, $1, Loangte: $2, $4×$5 Pixel, $3)',
	'timedmedia-long-general' => '(Ogg-Mediadoatäi, Loangte: $2, $3)',
	'timedmedia-long-error' => '(Uungultige Ogg-Doatäi: $1)',
	'timedmedia-more' => 'Optione …',
	'timedmedia-dismiss' => 'Sluute',
	'timedmedia-download' => 'Doatäi spiekerje',
	'timedmedia-desc-link' => 'Uur disse Doatäi',
);

/** Sundanese (Basa Sunda)
 * @author Kandar
 */
$messages['su'] = array(
	'timedmedia-short-audio' => 'Koropak sora $1 ogg, $2',
	'timedmedia-short-video' => 'Koropak vidéo $1 ogg, $2',
	'timedmedia-short-general' => 'Koropak média $1 ogg, $2',
	'timedmedia-long-audio' => '(Koropak sora $1 ogg, lilana $2, $3)',
	'timedmedia-long-video' => '(Koropak vidéo $1 ogg, lilana $2, $4×$5 piksel, $3)',
	'timedmedia-long-multiplexed' => '(Koropak sora/vidéo ogg multipléks, $1, lilana $2, $4×$5 piksel, $3 gembleng)',
	'timedmedia-long-general' => '(Koropak média ogg, lilana $2, $3)',
	'timedmedia-long-error' => '(Koropak ogg teu valid: $1)',
	'timedmedia-play' => 'Setél',
	'timedmedia-pause' => 'Eureun',
	'timedmedia-stop' => 'Anggeusan',
	'timedmedia-play-video' => 'Setél vidéo',
	'timedmedia-play-sound' => 'Setél sora',
	'timedmedia-player-oggPlugin' => 'Plugin ogg',
	'timedmedia-player-thumbnail' => 'Gambar statis wungkul',
	'timedmedia-player-selected' => '(pinilih)',
	'timedmedia-use-player' => 'Paké panyetél:',
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
	'timedmedia-short-audio' => 'Ogg $1 ljudfil, $2',
	'timedmedia-short-video' => 'Ogg $1 videofil, $2',
	'timedmedia-short-general' => 'Ogg $1 mediafil, $2',
	'timedmedia-long-audio' => '(Ogg $1 ljudfil, längd $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 videofil, längd $2, $4×$5 pixel, $3)',
	'timedmedia-long-multiplexed' => '(Ogg multiplexad ljud/video-fil, $1, längd $2, $4×$5 pixel, $3 totalt)',
	'timedmedia-long-general' => '(Ogg mediafil, längd $2, $3)',
	'timedmedia-long-error' => '(Felaktig timedmedia-fil: $1)',
	'timedmedia-play' => 'Spela upp',
	'timedmedia-pause' => 'Pausa',
	'timedmedia-stop' => 'Stoppa',
	'timedmedia-play-video' => 'Spela upp video',
	'timedmedia-play-sound' => 'Spela upp ljud',
	'timedmedia-no-player' => 'Tyvärr verkar det inte finnas någon mediaspelare som stöds installerad i ditt system. Det finns <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">spelare att ladda ner</a>.',
	'timedmedia-no-xiphqt' => 'Du verkar inte ha XiphQT-komponenten för QuickTime. Utan den kan inte QuickTime spela upp timedmedia-filer.Du kan <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ladda ner XiphQT</a> eller välja någon annan spelare.',
	'timedmedia-player-videoElement' => '<video>-element',
	'timedmedia-player-oggPlugin' => 'timedmedia-plugin',
	'timedmedia-player-thumbnail' => 'Endast stillbilder',
	'timedmedia-player-soundthumb' => 'Ingen spelare',
	'timedmedia-player-selected' => '(vald)',
	'timedmedia-use-player' => 'Välj mediaspelare:',
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
	'timedmedia-short-audio' => 'Ogg $1 శ్రావ్యక ఫైలు, $2',
	'timedmedia-short-video' => 'Ogg $1 వీడియో ఫైలు, $2',
	'timedmedia-short-general' => 'Ogg $1 మీడియా ఫైలు, $2',
	'timedmedia-long-audio' => '(Ogg $1 శ్రవణ ఫైలు, నిడివి $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 వీడియో ఫైలు, నిడివి $2, $4×$5 పిక్సెళ్ళు, $3)',
	'timedmedia-long-multiplexed' => '(ఓగ్ మల్టిప్లెక్సుడ్ శ్రవణ/దృశ్యక ఫైలు, $1, నిడివి $2, $4×$5 పిక్సెళ్ళు, $3 మొత్తం)',
	'timedmedia-long-general' => '(Ogg మీడియా ఫైలు, నిడివి $2, $3)',
	'timedmedia-long-error' => '(తప్పుడు ogg ఫైలు: $1)',
	'timedmedia-play' => 'ఆడించు',
	'timedmedia-pause' => 'ఆపు',
	'timedmedia-stop' => 'ఆపివేయి',
	'timedmedia-play-video' => 'వీడియోని ఆడించు',
	'timedmedia-play-sound' => 'శబ్ధాన్ని వినిపించు',
	'timedmedia-player-videoElement' => 'విహారిణిలో సహజాత తోడ్పాటు',
	'timedmedia-player-oggPlugin' => 'బ్రౌజరు ప్లగిన్',
	'timedmedia-player-thumbnail' => 'నిచ్చల చిత్రాలు మాత్రమే',
	'timedmedia-player-soundthumb' => 'ప్లేయర్ లేదు',
	'timedmedia-player-selected' => '(ఎంచుకున్నారు)',
	'timedmedia-use-player' => 'ప్లేయర్ ఉపయోగించు:',
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
	'timedmedia-short-audio' => 'Ogg $1 парвандаи савтӣ, $2',
	'timedmedia-short-video' => 'Ogg $1 парвандаи наворӣ, $2',
	'timedmedia-short-general' => 'Ogg $1 парвандаи расона, $2',
	'timedmedia-long-audio' => '(Ogg $1 парвандаи савтӣ, тӯл $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 парвандаи наворӣ, тӯл $2, $4×$5 пикселҳо, $3)',
	'timedmedia-long-multiplexed' => '(Парвандаи Ogg савтӣ/наворӣ печида, $1, тӯл $2, $4×$5 пикселҳо, дар маҷмӯъ $3)',
	'timedmedia-long-general' => '(Парвандаи расонаи Ogg, тӯл $2, $3)',
	'timedmedia-long-error' => '(Парвандаи ғайримиҷози ogg: $1)',
	'timedmedia-play' => 'Пахш',
	'timedmedia-pause' => 'Сукут',
	'timedmedia-stop' => 'Қатъ',
	'timedmedia-play-video' => 'Пахши навор',
	'timedmedia-play-sound' => 'Пахши овоз',
	'timedmedia-no-player' => 'Бубахшед, дастгоҳи шумо нармафзори пахшкунандаи муносибе надорад. Лутфан <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">як барномаи пахшкунандаро боргирӣ кунед</a>.',
	'timedmedia-no-xiphqt' => 'Афзунаи XiphQT барои QuickTime ба назар намерасад. QuickTime наметавонад бидуни ин афзуна парвандаҳои timedmedia-ро пахш кунад. Лутфан <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT-ро боргирӣ кунед</a>  ё дигар нармафзори пахшкунандаро интихоб намоед.',
	'timedmedia-player-videoElement' => 'унсури <наворӣ>',
	'timedmedia-player-oggPlugin' => 'Афзунаи ogg',
	'timedmedia-player-thumbnail' => 'Фақат акс ҳанӯз',
	'timedmedia-player-soundthumb' => 'Пахшкунанда нест',
	'timedmedia-player-selected' => '(интихобшуда)',
	'timedmedia-use-player' => 'Истифода аз пахшкунанда:',
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
	'timedmedia-short-audio' => 'Ogg $1 parvandai savtī, $2',
	'timedmedia-short-video' => 'Ogg $1 parvandai navorī, $2',
	'timedmedia-short-general' => 'Ogg $1 parvandai rasona, $2',
	'timedmedia-long-audio' => '(Ogg $1 parvandai savtī, tūl $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 parvandai navorī, tūl $2, $4×$5 pikselho, $3)',
	'timedmedia-long-multiplexed' => "(Parvandai Ogg savtī/navorī pecida, $1, tūl $2, $4×$5 pikselho, dar maçmū' $3)",
	'timedmedia-long-general' => '(Parvandai rasonai Ogg, tūl $2, $3)',
	'timedmedia-long-error' => '(Parvandai ƣajrimiçozi ogg: $1)',
	'timedmedia-play' => 'Paxş',
	'timedmedia-pause' => 'Sukut',
	'timedmedia-stop' => "Qat'",
	'timedmedia-play-video' => 'Paxşi navor',
	'timedmedia-play-sound' => 'Paxşi ovoz',
	'timedmedia-no-player' => 'Bubaxşed, dastgohi şumo narmafzori paxşkunandai munosibe nadorad. Lutfan <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">jak barnomai paxşkunandaro borgirī kuned</a>.',
	'timedmedia-no-xiphqt' => 'Afzunai XiphQT baroi QuickTime ba nazar namerasad. QuickTime nametavonad biduni in afzuna parvandahoi timedmedia-ro paxş kunad. Lutfan <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT-ro borgirī kuned</a>  jo digar narmafzori paxşkunandaro intixob namoed.',
	'timedmedia-player-thumbnail' => 'Faqat aks hanūz',
	'timedmedia-player-soundthumb' => 'Paxşkunanda nest',
	'timedmedia-player-selected' => '(intixobşuda)',
	'timedmedia-use-player' => 'Istifoda az paxşkunanda:',
	'timedmedia-more' => 'Beştar...',
	'timedmedia-dismiss' => 'Bastan',
	'timedmedia-download' => 'Borgiriji parvanda',
	'timedmedia-desc-link' => 'Dar borai in parvanda',
);

/** Thai (ไทย)
 * @author Manop
 * @author Woraponboonkerd
 */
$messages['th'] = array(
	'timedmedia-play' => 'เล่น',
	'timedmedia-pause' => 'หยุดชั่วคราว',
	'timedmedia-stop' => 'หยุด',
	'timedmedia-play-video' => 'เล่นวิดีโอ',
	'timedmedia-play-sound' => 'เล่นเสียง',
	'timedmedia-no-player' => 'ขออภัย ระบบของคุณไม่มีซอฟต์แวร์ที่สนับสนุนไฟล์สื่อนี้
กรุณา<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ดาวน์โหลดซอฟต์แวร์เล่นสื่อ</a>',
	'timedmedia-no-xiphqt' => 'ไม่พบซอฟต์แวร์เสริม XiphQT ของโปรแกรม QuickTime บนระบบของคุณ
โปรแกรม QuickTime ไม่สามารถเล่นไฟล์สกุล Ogg ได้ถ้าไม่มีโปรแกรมเสริมนี้
กรุณา<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ดวาน์โหลด XiphQT</a> หรือเลือกโปรแกรมอื่น',
);

/** Turkmen (Türkmençe)
 * @author Hanberke
 */
$messages['tk'] = array(
	'timedmedia-desc' => 'Ogg Theora we Vorbis faýllary üçin işleýji, JavaScript pleýeri bilen bilelikde',
	'timedmedia-short-audio' => 'Ogg $1 ses faýly, $2',
	'timedmedia-short-video' => 'Ogg $1 wideo faýly, $2',
	'timedmedia-short-general' => 'Ogg $1 media faýly, $2',
	'timedmedia-long-audio' => '(Ogg $1 ses faýly, uzynlyk $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 wideo faýly, uzynlyk $2, $4×$5 piksel, $3)',
	'timedmedia-long-multiplexed' => '(Ogg multipleks audio/wideo faýly, $1, uzynlyk $2, $4×$5 piksel, $3 jemi)',
	'timedmedia-long-general' => '(Ogg media faýly, uzynlyk $2, $3)',
	'timedmedia-long-error' => '(Nädogry ogg faýly: $1)',
	'timedmedia-play' => 'Oýnat',
	'timedmedia-pause' => 'Pauza',
	'timedmedia-stop' => 'Duruz',
	'timedmedia-play-video' => 'Wideo oýnat',
	'timedmedia-play-sound' => 'Ses oýnat',
	'timedmedia-no-player' => 'Gynansak-da, ulgamyňyzda goldanylýan haýsydyr bir pleýer programmaňyz ýok ýaly-la.
<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download"> Pleýer düşüriň</a>.',
	'timedmedia-no-xiphqt' => 'QuickTime üçin XiphQT komponentiňiz ýok bolarly.
QuickTime bu komponent bolmasa Ogg faýllaryny oýnadyp bilmeýär.
<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT-i düşüriň</a> ýa-da başga bir pleýer saýlaň.',
	'timedmedia-player-videoElement' => 'Milli brauzer goldawy',
	'timedmedia-player-oggPlugin' => 'Brauzer goşmaça moduly',
	'timedmedia-player-thumbnail' => 'Diňe hereketsiz surat',
	'timedmedia-player-soundthumb' => 'Pleýer ýok',
	'timedmedia-player-selected' => '(saýlanylan)',
	'timedmedia-use-player' => 'Pleýer ulan:',
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
	'timedmedia-short-audio' => '$1 na talaksang pangtunog ng Ogg, $2',
	'timedmedia-short-video' => "$1 talaksang pampalabas (''video'') ng Ogg, $2",
	'timedmedia-short-general' => '$1 talaksang pangmidya ng Ogg, $2',
	'timedmedia-long-audio' => '($1 talaksang pantunog ng Ogg, haba $2, $3)',
	'timedmedia-long-video' => '($1 talaksan ng palabas ng Ogg, haba $2, $4×$5 mga piksel, $3)',
	'timedmedia-long-multiplexed' => '(magkasanib at nagsasabayang talaksang nadirinig o audio/palabas ng Ogg, $1, haba $2, $4×$5 mga piksel, $3 sa kalahatan)',
	'timedmedia-long-general' => "(Talaksang pangmidya ng ''Ogg'', haba $2, $3)",
	'timedmedia-long-error' => "(Hindi tanggap na talaksang ''ogg'': $1)",
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
	'timedmedia-short-audio' => 'Ogg $1 ses dosyası, $2',
	'timedmedia-short-video' => 'Ogg $1 film dosyası, $2',
	'timedmedia-short-general' => 'Ogg $1 medya dosyası, $2',
	'timedmedia-long-audio' => '(Ogg $1 ses dosyası, süre $2, $3)',
	'timedmedia-long-video' => '(Ogg $1 film dosyası, süre $2, $4×$5 piksel, $3)',
	'timedmedia-long-multiplexed' => '(Ogg çok düzeyli ses/film dosyası, $1, süre $2, $4×$5 piksel, $3 genelde)',
	'timedmedia-long-general' => '(Ogg medya dosyası, süre $2, $3)',
	'timedmedia-long-error' => '(Geçersiz ogg dosyası: $1)',
	'timedmedia-play' => 'Oynat',
	'timedmedia-pause' => 'Duraklat',
	'timedmedia-stop' => 'Durdur',
	'timedmedia-play-video' => 'Video filmini oynat',
	'timedmedia-play-sound' => 'Sesi oynat',
	'timedmedia-no-player' => 'Üzgünüz, sisteminiz desteklenen herhangi bir oynatıcı yazılımına sahip gibi görünmüyor.
Lütfen <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">bir oynatıcı indirin</a>.',
	'timedmedia-no-xiphqt' => 'QuickTime için XiphQT bileşenine sahip değil görünüyorsunuz.
QuickTime bu bileşen olmadan Ogg dosyalarını oynatamaz.
Lütfen <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT\'i indirin</a> ya da başka bir oynatıcı seçin.',
	'timedmedia-player-videoElement' => 'Yerel tarayıcı desteği',
	'timedmedia-player-oggPlugin' => 'Tarayıcı eklentisi',
	'timedmedia-player-thumbnail' => 'Henüz sadece resimdir',
	'timedmedia-player-soundthumb' => 'Oynatıcı yok',
	'timedmedia-player-selected' => '(seçilmiş)',
	'timedmedia-use-player' => 'Oynatıcıyı kullanın:',
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

/** Ukrainian (Українська)
 * @author AS
 * @author Ahonc
 * @author NickK
 * @author Prima klasy4na
 */
$messages['uk'] = array(
	'timedmedia-desc' => 'Оброблювач файлів Ogg Theora і Vorbis з використанням JavaScript-програвача',
	'timedmedia-short-audio' => 'Звуковий файл Ogg $1, $2',
	'timedmedia-short-video' => 'Відео-файл Ogg $1, $2',
	'timedmedia-short-general' => 'Файл Ogg $1, $2',
	'timedmedia-long-audio' => '(звуковий файл Ogg $1, довжина $2, $3)',
	'timedmedia-long-video' => '(відео-файл Ogg $1, довжина $2, $4×$5 пікселів, $3)',
	'timedmedia-long-multiplexed' => '(мультиплексний аудіо/відео-файл ogg, $1, довжина $2, $4×$5 пікселів, $3 усього)',
	'timedmedia-long-general' => '(медіа-файл Ogg, довжина $2, $3)',
	'timedmedia-long-error' => '(Неправильний timedmedia-файл: $1)',
	'timedmedia-play' => 'Відтворити',
	'timedmedia-pause' => 'Пауза',
	'timedmedia-stop' => 'Зупинити',
	'timedmedia-play-video' => 'Відтворити відео',
	'timedmedia-play-sound' => 'Відтворити звук',
	'timedmedia-no-player' => 'Вибачте, ваша ситема не має необхідного програмного забезпечення для відтворення файлів. Будь ласка, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">завантажте програвач</a>.',
	'timedmedia-no-xiphqt' => 'Відсутній компонент XiphQT для QuickTime.
QuickTime не може відтворювати timedmedia-файли без цього компонента.
Будь ласка, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">завантажте XiphQT</a> або оберіть інший програвач.',
	'timedmedia-player-videoElement' => 'Рідна підтримка веб-оглядача',
	'timedmedia-player-oggPlugin' => 'Плаґін для браузера',
	'timedmedia-player-thumbnail' => 'Тільки нерухоме зображення',
	'timedmedia-player-soundthumb' => 'Нема програвача',
	'timedmedia-player-selected' => '(обраний)',
	'timedmedia-use-player' => 'Використовувати програвач:',
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
	'timedmedia-short-audio' => 'File audio Ogg $1, $2',
	'timedmedia-short-video' => 'File video Ogg $1, $2',
	'timedmedia-short-general' => 'File multimedial Ogg $1, $2',
	'timedmedia-long-audio' => '(File audio Ogg $1, durata $2, $3)',
	'timedmedia-long-video' => '(File video Ogg $1, durata $2, dimensioni $4×$5 pixel, $3)',
	'timedmedia-long-multiplexed' => '(File audio/video multiplexed Ogg $1, durata $2, dimensioni $4×$5 pixel, conplessivamente $3)',
	'timedmedia-long-general' => '(File multimedial Ogg, durata $2, $3)',
	'timedmedia-long-error' => '(File ogg mìa valido: $1)',
	'timedmedia-play' => 'Riprodusi',
	'timedmedia-pause' => 'Pausa',
	'timedmedia-stop' => 'Fèrma',
	'timedmedia-play-video' => 'Varda el video',
	'timedmedia-play-sound' => 'Scolta el file',
	'timedmedia-no-player' => 'Semo spiacenti, ma sul to sistema no risulta instalà nissun software de riproduzion conpatibile. Par piaser <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">scàrichete un letor</a> che vaga ben.',
	'timedmedia-no-xiphqt' => 'No risulta mìa instalà el conponente XiphQT de QuickTime. Senza sto conponente no se pode mìa riprodur i file Ogg con QuickTime. Par piaser, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">scàrichete XiphQT</a> o siegli n\'altro letor.',
	'timedmedia-player-videoElement' => 'Suporto browser zà de suo (nativo)',
	'timedmedia-player-oggPlugin' => 'Plugin browser',
	'timedmedia-player-thumbnail' => 'Solo imagini fisse',
	'timedmedia-player-soundthumb' => 'Nissun letor',
	'timedmedia-player-selected' => '(selezionà)',
	'timedmedia-use-player' => 'Dòpara el letor:',
	'timedmedia-more' => 'Altro...',
	'timedmedia-dismiss' => 'Sara',
	'timedmedia-download' => 'Descarga el file',
	'timedmedia-desc-link' => 'Informazion su sto file',
);

/** Veps (Vepsan kel')
 * @author Игорь Бродский
 */
$messages['vep'] = array(
	'timedmedia-play' => 'Väta',
	'timedmedia-pause' => 'Pauz',
	'timedmedia-stop' => 'Azotada',
	'timedmedia-play-video' => 'Ozutada video',
	'timedmedia-play-sound' => 'Väta kulundad',
	'timedmedia-player-oggPlugin' => 'Kaclim-plagin',
	'timedmedia-player-soundthumb' => 'Ei ole plejerad',
	'timedmedia-player-selected' => '(valitud)',
	'timedmedia-use-player' => 'Kävutada plejer:',
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
	'timedmedia-short-audio' => 'Tập tin âm thanh Ogg $1, $2',
	'timedmedia-short-video' => 'Tập tin video Ogg $1, $2',
	'timedmedia-short-general' => 'Tập tin Ogg $1, $2',
	'timedmedia-long-audio' => '(tập tin âm thanh Ogg $1, dài $2, $3)',
	'timedmedia-long-video' => '(tập tin video Ogg $1, dài $2, $4×$5 điểm ảnh, $3)',
	'timedmedia-long-multiplexed' => '(tập tin Ogg có âm thanh và video ghép kênh, $1, dài $2, $4×$5 điểm ảnh, $3 tất cả)',
	'timedmedia-long-general' => '(tập tin phương tiện Ogg, dài $2, $3)',
	'timedmedia-long-error' => '(Tập tin Ogg có lỗi: $1)',
	'timedmedia-play' => 'Chơi',
	'timedmedia-pause' => 'Tạm ngừng',
	'timedmedia-stop' => 'Ngừng',
	'timedmedia-play-video' => 'Coi video',
	'timedmedia-play-sound' => 'Nghe âm thanh',
	'timedmedia-no-player' => 'Rất tiếc, hình như máy tính của bạn cần thêm phần mềm. Xin <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/vi">tải xuống chương trình chơi nhạc</a>.',
	'timedmedia-no-xiphqt' => 'Hình như bạn không có bộ phận XiphQT cho QuickTime, nên QuickTime không thể chơi những tập tin Ogg được. Xin <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/vi">truyền xuống XiphQT</a> hay chọn một chương trình chơi nhạc khác.',
	'timedmedia-player-videoElement' => 'Bộ chơi có sẵn trong trình duyệt',
	'timedmedia-player-oggPlugin' => 'Phần bổ trợ trình duyệt',
	'timedmedia-player-thumbnail' => 'Chỉ hiển thị hình tĩnh',
	'timedmedia-player-soundthumb' => 'Tắt',
	'timedmedia-player-selected' => '(được chọn)',
	'timedmedia-use-player' => 'Chọn chương trình chơi:',
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
	'timedmedia-player-videoElement' => 'Stüt bevüresodanaföm gebidon',
	'timedmedia-more' => 'Pluikos...',
	'timedmedia-dismiss' => 'Färmükön',
	'timedmedia-download' => 'Donükön ragivi',
	'timedmedia-desc-link' => 'Tefü ragiv at',
);

/** Walloon (Walon) */
$messages['wa'] = array(
	'timedmedia-dismiss' => 'Clôre',
);

/** Cantonese (粵語) */
$messages['yue'] = array(
	'timedmedia-desc' => 'Ogg Theora 同 Vorbis 檔案嘅處理器，加埋 JavaScript 播放器',
	'timedmedia-short-audio' => 'Ogg $1 聲檔，$2',
	'timedmedia-short-video' => 'Ogg $1 畫檔，$2',
	'timedmedia-short-general' => 'Ogg $1 媒檔，$2',
	'timedmedia-long-audio' => '(Ogg $1 聲檔，長度$2，$3)',
	'timedmedia-long-video' => '(Ogg $1 畫檔，長度$2，$4×$5像素，$3)',
	'timedmedia-long-multiplexed' => '(Ogg 多工聲／畫檔，$1，長度$2，$4×$5像素，總共$3)',
	'timedmedia-long-general' => '(Ogg 媒檔，長度$2，$3)',
	'timedmedia-long-error' => '(無效嘅ogg檔: $1)',
	'timedmedia-play' => '去',
	'timedmedia-pause' => '暫停',
	'timedmedia-stop' => '停',
	'timedmedia-play-video' => '去畫',
	'timedmedia-play-sound' => '去聲',
	'timedmedia-no-player' => '對唔住，你嘅系統並無任何可以支援得到嘅播放器。請安裝<a href="http://www.java.com/zh_TW/download/manual.jsp">Java</a>。',
	'timedmedia-no-xiphqt' => '你似乎無畀QuickTime用嘅XiphQT組件。響未有呢個組件嗰陣，QuickTime係唔可以播放Ogg檔案。請<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">下載XiphQT</a>或者揀過另外一個播放器。',
	'timedmedia-player-videoElement' => '<video>元素',
	'timedmedia-player-oggPlugin' => 'Ogg插件',
	'timedmedia-player-thumbnail' => '只有靜止圖像',
	'timedmedia-player-soundthumb' => '無播放器',
	'timedmedia-player-selected' => '(揀咗)',
	'timedmedia-use-player' => '使用播放器:',
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
	'timedmedia-short-audio' => 'Ogg $1 声音文件，$2',
	'timedmedia-short-video' => 'Ogg $1 视频文件，$2',
	'timedmedia-short-general' => 'Ogg $1 媒体文件，$2',
	'timedmedia-long-audio' => '（Ogg $1 声音文件，长度$2，$3）',
	'timedmedia-long-video' => '（Ogg $1 视频文件，长度$2，$4×$5像素，$3）',
	'timedmedia-long-multiplexed' => '（Ogg 多工声音／视频文件，$1，长度$2，$4×$5像素，共$3）',
	'timedmedia-long-general' => '（Ogg 媒体文件，长度$2，$3）',
	'timedmedia-long-error' => '（无效的ogg文件: $1）',
	'timedmedia-play' => '播放',
	'timedmedia-pause' => '暂停',
	'timedmedia-stop' => '停止',
	'timedmedia-play-video' => '播放视频',
	'timedmedia-play-sound' => '播放声音',
	'timedmedia-no-player' => '抱歉，您的系统并无任何可以支持播放的播放器。请安装<a href="http://www.java.com/zh_CN/download/manual.jsp">Java</a>。',
	'timedmedia-no-xiphqt' => '您似乎没有给QuickTime用的XiphQT组件。在未有这个组件的情况下，QuickTime是不能播放Ogg文件的。请<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">下载XiphQT</a>或者选取另一个播放器。',
	'timedmedia-player-videoElement' => '<video>元素',
	'timedmedia-player-oggPlugin' => 'Ogg插件',
	'timedmedia-player-thumbnail' => '只有静止图像',
	'timedmedia-player-soundthumb' => '沒有播放器',
	'timedmedia-player-selected' => '（已选取）',
	'timedmedia-use-player' => '使用播放器:',
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
	'timedmedia-short-audio' => 'Ogg $1 聲音檔案，$2',
	'timedmedia-short-video' => 'Ogg $1 影片檔案，$2',
	'timedmedia-short-general' => 'Ogg $1 媒體檔案，$2',
	'timedmedia-long-audio' => '（Ogg $1 聲音檔案，長度$2，$3）',
	'timedmedia-long-video' => '（Ogg $1 影片檔案，長度$2，$4×$5像素，$3）',
	'timedmedia-long-multiplexed' => '（Ogg 多工聲音／影片檔案，$1，長度$2，$4×$5像素，共$3）',
	'timedmedia-long-general' => '（Ogg 媒體檔案，長度$2，$3）',
	'timedmedia-long-error' => '（無效的ogg檔案: $1）',
	'timedmedia-play' => '播放',
	'timedmedia-pause' => '暫停',
	'timedmedia-stop' => '停止',
	'timedmedia-play-video' => '播放影片',
	'timedmedia-play-sound' => '播放聲音',
	'timedmedia-no-player' => '抱歉，您的系統並無任何可以支援播放的播放器。請安裝<a href="http://www.java.com/zh_TW/download/manual.jsp">Java</a>。',
	'timedmedia-no-xiphqt' => '您似乎沒有給QuickTime用的XiphQT組件。在未有這個組件的情況下，QuickTime是不能播放Ogg檔案的。請<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">下載XiphQT</a>或者選取另一個播放器。',
	'timedmedia-player-videoElement' => '<video>元素',
	'timedmedia-player-oggPlugin' => 'Ogg插件',
	'timedmedia-player-thumbnail' => '只有靜止圖片',
	'timedmedia-player-soundthumb' => '沒有播放器',
	'timedmedia-player-selected' => '（已選取）',
	'timedmedia-use-player' => '使用播放器:',
	'timedmedia-more' => '更多...',
	'timedmedia-dismiss' => '關閉',
	'timedmedia-download' => '下載檔案',
	'timedmedia-desc-link' => '關於這個檔案',
);

