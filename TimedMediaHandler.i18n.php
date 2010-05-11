<?php
/**
 * Internationalisation file for extension OggPlayer.
 *
 * @addtogroup Extensions
 */

$messages = array();

$messages['en'] = array(
	'tmh-desc'             => 'Handler for Timed Media ( video, audio, timedText ) with transcoding to Ogg Theora/Vorbis',
	'tmh-short-audio'      => 'Ogg $1 sound file, $2',
	'tmh-short-video'      => 'Ogg $1 video file, $2',
	'tmh-short-general'    => 'Ogg $1 media file, $2',
	'tmh-long-audio'       => '(Ogg $1 sound file, length $2, $3)',
	'tmh-long-video'       => '(Ogg $1 video file, length $2, $4×$5 pixels, $3)',
	'tmh-long-multiplexed' => '(Ogg multiplexed audio/video file, $1, length $2, $4×$5 pixels, $3 overall)',
	'tmh-long-general'     => '(Ogg media file, length $2, $3)',
	'tmh-long-error'       => '(Invalid ogg file: $1)',
	'tmh-play'             => 'Play',
	'tmh-pause'            => 'Pause',
	'tmh-stop'             => 'Stop',
	'tmh-play-video'       => 'Play video',
	'tmh-play-sound'       => 'Play sound',
	'tmh-no-player'        => 'Sorry, your system does not appear to have any supported player software.
Please <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">download a player</a>.',
	'tmh-no-xiphqt'        => 'You do not appear to have the XiphQT component for QuickTime.
QuickTime cannot play Ogg files without this component.
Please <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">download XiphQT</a> or choose another player.',

	'tmh-player-videoElement' => 'Native browser support',
	'tmh-player-oggPlugin' => 'Browser plugin',
	'tmh-player-cortado'   => 'Cortado (Java)', # only translate this message to other languages if you have to change it
	'tmh-player-vlc-mozilla' => 'VLC', # only translate this message to other languages if you have to change it
	'tmh-player-vlc-activex' => 'VLC (ActiveX)', # only translate this message to other languages if you have to change it
	'tmh-player-quicktime-mozilla' => 'QuickTime', # only translate this message to other languages if you have to change it
	'tmh-player-quicktime-activex' => 'QuickTime (ActiveX)', # only translate this message to other languages if you have to change it
	'tmh-player-totem'     => 'Totem', # only translate this message to other languages if you have to change it
	'tmh-player-kmplayer'  => 'KMPlayer', # only translate this message to other languages if you have to change it
	'tmh-player-kaffeine'  => 'Kaffeine', # only translate this message to other languages if you have to change it
	'tmh-player-mplayerplug-in' => 'mplayerplug-in', # only translate this message to other languages if you have to change it
	'tmh-player-thumbnail' => 'Still image only',
	'tmh-player-soundthumb' => 'No player',
	'tmh-player-selected'  => '(selected)',
	'tmh-use-player'       => 'Use player:',
	'tmh-more'             => 'More…',
	'tmh-dismiss'          => 'Close',
	'tmh-download'         => 'Download file',
	'tmh-desc-link'        => 'About this file',
	'tmh-oggThumb-version' => 'OggHandler requires oggThumb version $1 or later.',
	'tmh-oggThumb-failed'  => 'oggThumb failed to create the thumbnail.',
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
	'tmh-desc' => '{{desc}}',
	'tmh-short-general' => 'File details for generic (non-audio, non-video) Ogg files, short version.
Parameters are:
* $1 file type, e.g. Vorbis, Speex
* $2 ?',
	'tmh-long-audio' => 'File details for Ogg files, shown after the filename in the image description page.
Parameters are:
* $1 file codec, f.e. Vorbis, Speex
* $2 file duration, f.e. 1m34s
* $3 file sampling rate, f.e. 97kbps',
	'tmh-play' => '{{Identical|Play}}',
	'tmh-player-videoElement' => 'Message used in JavaScript.',
	'tmh-player-vlc-mozilla' => '{{optional}}',
	'tmh-player-quicktime-mozilla' => '{{optional}}',
	'tmh-player-totem' => '{{optional}}',
	'tmh-player-kmplayer' => '{{optional}}',
	'tmh-player-kaffeine' => '{{optional}}',
	'tmh-more' => '{{Identical|More...}}',
	'tmh-dismiss' => '{{Identical|Close}}',
	'tmh-download' => '{{Identical|Download}}',
);

/** Albaamo innaaɬiilka (Albaamo innaaɬiilka)
 * @author Ulohnanne
 */
$messages['akz'] = array(
	'tmh-more' => 'Maatàasasi...',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 * @author SPQRobin
 */
$messages['af'] = array(
	'tmh-desc' => "Hanteer Ogg Theora- en Vorbis-lêers met 'n JavaScript-mediaspeler",
	'tmh-short-audio' => 'Ogg $1 klanklêer, $2',
	'tmh-short-video' => 'Ogg $1 video lêer, $2',
	'tmh-short-general' => 'Ogg $1 medialêer, $2',
	'tmh-long-audio' => '(Ogg $1 klanklêer, lengte $2, $3)',
	'tmh-long-video' => '(Ogg $1 videolêer, lengte $2, $4×$5 pixels, $3)',
	'tmh-long-general' => '(Ogg medialêer, lengte $2, $3)',
	'tmh-long-error' => '(Ongeldige tmh-lêer: $1)',
	'tmh-play' => 'Speel',
	'tmh-pause' => 'Wag',
	'tmh-stop' => 'Stop',
	'tmh-play-video' => 'Speel video',
	'tmh-play-sound' => 'Speel geluid',
	'tmh-player-videoElement' => 'Standaardondersteuning in webblaaier',
	'tmh-player-oggPlugin' => 'Webblaaier-plugin',
	'tmh-player-soundthumb' => 'Geen mediaspeler',
	'tmh-player-selected' => '(geselekteer)',
	'tmh-use-player' => 'Gebruik speler:',
	'tmh-more' => 'Meer…',
	'tmh-dismiss' => 'Sluit',
	'tmh-download' => 'Laai lêer af',
	'tmh-desc-link' => 'Aangaande die lêer',
);

/** Aragonese (Aragonés)
 * @author Juanpabl
 */
$messages['an'] = array(
	'tmh-desc' => 'Manullador ta archibos Ogg Theora and Vorbis, con un reproductor JavaScript',
	'tmh-short-audio' => 'Archibo de son ogg $1, $2',
	'tmh-short-video' => 'Archibo de bidio ogg $1, $2',
	'tmh-short-general' => 'Archibo multimedia ogg $1, $2',
	'tmh-long-audio' => '(Archibo de son ogg $1, durada $2, $3)',
	'tmh-long-video' => '(Archibo de bidio ogg $1, durada $2, $4×$5 píxels, $3)',
	'tmh-long-multiplexed' => '(archibo ogg multiplexato audio/bidio, $1, durada $2, $4×$5 píxels, $3 total)',
	'tmh-long-general' => '(archibo ogg multimedia durada $2, $3)',
	'tmh-long-error' => '(Archibo ogg no conforme: $1)',
	'tmh-play' => 'Reproduzir',
	'tmh-pause' => 'Pausa',
	'tmh-stop' => 'Aturar',
	'tmh-play-video' => 'Reproduzir bidio',
	'tmh-play-sound' => 'Reproduzir son',
	'tmh-no-player' => 'No puedo trobar garra software reproductor suportato.
Abría d\'<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">escargar un reproductor</a>.',
	'tmh-no-xiphqt' => 'No puedo trobar o component XiphQT ta QuickTime.
QuickTime no puede reproduzir archibos ogg sin este component.
Puede <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">escargar XiphQT</a> u trigar un atro reproductor.',
	'tmh-player-videoElement' => "Soporte natibo d'o nabegador",
	'tmh-player-oggPlugin' => "Plugin d'o nabegador",
	'tmh-player-thumbnail' => 'Nomás imachen fixa',
	'tmh-player-soundthumb' => 'Garra reproductor',
	'tmh-player-selected' => '(trigato)',
	'tmh-use-player' => 'Fer serbir o reprodutor:',
	'tmh-more' => 'Más…',
	'tmh-dismiss' => 'Zarrar',
	'tmh-download' => 'Escargar archibo',
	'tmh-desc-link' => 'Informazión sobre este archibo',
);

/** Arabic (العربية)
 * @author Alnokta
 * @author Meno25
 * @author OsamaK
 */
$messages['ar'] = array(
	'tmh-desc' => 'متحكم لملفات Ogg Theora وVorbis، مع لاعب جافاسكريت',
	'tmh-short-audio' => 'Ogg $1 ملف صوت، $2',
	'tmh-short-video' => 'Ogg $1 ملف فيديو، $2',
	'tmh-short-general' => 'Ogg $1 ملف ميديا، $2',
	'tmh-long-audio' => '(Ogg $1 ملف صوت، الطول $2، $3)',
	'tmh-long-video' => '(Ogg $1 ملف فيديو، الطول $2، $4×$5 بكسل، $3)',
	'tmh-long-multiplexed' => '(ملف Ogg مالتي بليكسد أوديو/فيديو، $1، الطول $2، $4×$5 بكسل، $3 إجمالي)',
	'tmh-long-general' => '(ملف ميديا Ogg، الطول $2، $3)',
	'tmh-long-error' => '(ملف Ogg غير صحيح: $1)',
	'tmh-play' => 'عرض',
	'tmh-pause' => 'إيقاف مؤقت',
	'tmh-stop' => 'إيقاف',
	'tmh-play-video' => 'عرض الفيديو',
	'tmh-play-sound' => 'عرض الصوت',
	'tmh-no-player' => 'معذرة ولكن يبدو أنه لا يوجد لديك برنامج عرض مدعوم. من فضلك ثبت <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">الجافا</a>.',
	'tmh-no-xiphqt' => 'لا يبدو أنك تملك مكون XiphQT لكويك تايم.
كويك تايم لا يمكنه عرض ملفات Ogg بدون هذا المكون.
من فضلك <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">حمل XiphQT</a> أو اختر برنامجا آخر.',
	'tmh-player-videoElement' => 'دعم متصفح مدمج',
	'tmh-player-oggPlugin' => 'إضافة متصفح',
	'tmh-player-cortado' => 'كورتادو (جافا)',
	'tmh-player-vlc-mozilla' => 'في إل سي',
	'tmh-player-vlc-activex' => 'في إل سي (أكتيف إكس)',
	'tmh-player-quicktime-mozilla' => 'كويك تايم',
	'tmh-player-quicktime-activex' => 'كويك تايم (أكتيف إكس)',
	'tmh-player-totem' => 'توتيم',
	'tmh-player-kmplayer' => 'كيه إم بلاير',
	'tmh-player-kaffeine' => 'كافيين',
	'tmh-player-mplayerplug-in' => 'إضافة إم بلاير',
	'tmh-player-thumbnail' => 'مازال صورة فقط',
	'tmh-player-soundthumb' => 'لا برنامج',
	'tmh-player-selected' => '(مختار)',
	'tmh-use-player' => 'استخدم البرنامج:',
	'tmh-more' => 'المزيد...',
	'tmh-dismiss' => 'إغلاق',
	'tmh-download' => 'نزل الملف',
	'tmh-desc-link' => 'عن هذا الملف',
);

/** Aramaic (ܐܪܡܝܐ)
 * @author Basharh
 */
$messages['arc'] = array(
	'tmh-more' => 'ܝܬܝܪ…',
);

/** Egyptian Spoken Arabic (مصرى)
 * @author Ghaly
 * @author Meno25
 * @author Ramsis II
 */
$messages['arz'] = array(
	'tmh-desc' => 'متحكم لملفات أو جى جى ثيورا و فوربيس، مع بلاير جافاسكريبت',
	'tmh-short-audio' => 'Ogg $1 ملف صوت، $2',
	'tmh-short-video' => 'Ogg $1 ملف فيديو, $2',
	'tmh-short-general' => 'Ogg $1 ملف ميديا، $2',
	'tmh-long-audio' => '(Ogg $1 ملف صوت، الطول $2، $3)',
	'tmh-long-video' => '(Ogg $1 ملف فيديو، الطول $2، $4×$5 بكسل، $3)',
	'tmh-long-multiplexed' => '(ملف Ogg مالتى بليكسد أوديو/فيديو، $1، الطول $2، $4×$5 بكسل، $3 إجمالي)',
	'tmh-long-general' => '(ملف ميديا Ogg، الطول $2، $3)',
	'tmh-long-error' => '(ملف ogg مش صحيح: $1)',
	'tmh-play' => 'شغل',
	'tmh-pause' => ' توقيف مؤقت',
	'tmh-stop' => 'توقيف',
	'tmh-play-video' => 'شغل الفيديو',
	'tmh-play-sound' => 'شغل الصوت',
	'tmh-no-player' => 'متاسفين الظاهر أنه ماعندكش برنامج عرض مدعوم.
لو سمحت تنزل < a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">الجافا</a>.',
	'tmh-no-xiphqt' => 'الظاهر انه ماعندكش مكون الـ XiphQT لكويك تايم.
كويك تايم مش ممكن يعرض ملفات Ogg  من غير المكون دا.
لو سمحت <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">تنزل XiphQT</a> أو تختار برنامج تانى.',
	'tmh-player-videoElement' => 'دعم البراوزر الاصلي',
	'tmh-player-oggPlugin' => 'اضافة براوزر',
	'tmh-player-cortado' => 'كورتادو (جافا)',
	'tmh-player-vlc-mozilla' => 'فى إل سي',
	'tmh-player-vlc-activex' => 'فى إل سى (أكتيف إكس)',
	'tmh-player-quicktime-mozilla' => 'كويك تايم',
	'tmh-player-quicktime-activex' => 'كويك تايم (أكتيف إكس)',
	'tmh-player-totem' => 'توتيم',
	'tmh-player-kmplayer' => 'كيه إم بلاير',
	'tmh-player-kaffeine' => 'كافيين',
	'tmh-player-mplayerplug-in' => 'إضافة إم بلاير',
	'tmh-player-thumbnail' => 'صورة ثابتة بس',
	'tmh-player-soundthumb' => 'ما فيش برنامج',
	'tmh-player-selected' => '(مختار)',
	'tmh-use-player' => 'استخدم البرنامج:',
	'tmh-more' => 'أكتر...',
	'tmh-dismiss' => 'اقفل',
	'tmh-download' => 'نزل الملف',
	'tmh-desc-link' => 'عن الملف دا',
);

/** Asturian (Asturianu)
 * @author Esbardu
 */
$messages['ast'] = array(
	'tmh-desc' => "Remanador d'archivos Ogg Theora y Vorbis, con un reproductor JavaScript",
	'tmh-short-audio' => 'Archivu de soníu ogg $1, $2',
	'tmh-short-video' => 'Archivu de videu ogg $1, $2',
	'tmh-short-general' => 'Archivu multimedia ogg $1, $2',
	'tmh-long-audio' => '(Archivu de soníu ogg $1, llonxitú $2, $3)',
	'tmh-long-video' => '(Archivu de videu ogg $1, llonxitú $2, $4×$5 píxeles, $3)',
	'tmh-long-multiplexed' => "(Archivu d'audiu/videu ogg multiplexáu, $1, llonxitú $2, $4×$5 píxeles, $3)",
	'tmh-long-general' => '(Archivu multimedia ogg, llonxitú $2, $3)',
	'tmh-long-error' => '(Archivu ogg non válidu: $1)',
	'tmh-play' => 'Reproducir',
	'tmh-pause' => 'Pausar',
	'tmh-stop' => 'Aparar',
	'tmh-play-video' => 'Reproducir videu',
	'tmh-play-sound' => 'Reproducir soníu',
	'tmh-no-player' => 'Sentímoslo, el to sistema nun paez tener nengún de los reproductores soportaos. Por favor <a
href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">descarga un reproductor</a>.',
	'tmh-no-xiphqt' => 'Paez que nun tienes el componente XiphQT pa QuickTime. QuickTime nun pue reproducr archivos ogg ensin esti componente. Por favor <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">descarga XiphQT</a> o escueyi otru reproductor.',
	'tmh-player-videoElement' => 'Soporte nativu del navegador',
	'tmh-player-oggPlugin' => 'Plugin del navegador',
	'tmh-player-thumbnail' => 'Namái imaxe en pausa',
	'tmh-player-soundthumb' => 'Nun hai reproductor',
	'tmh-player-selected' => '(seleicionáu)',
	'tmh-use-player' => 'Utilizar el reproductor:',
	'tmh-more' => 'Más...',
	'tmh-dismiss' => 'Zarrar',
	'tmh-download' => 'Descargar archivu',
	'tmh-desc-link' => 'Tocante a esti archivu',
);

/** Kotava (Kotava)
 * @author Sab
 */
$messages['avk'] = array(
	'tmh-download' => 'Iyeltakkalvajara',
	'tmh-desc-link' => 'Icde bat iyeltak',
);

/** Samogitian (Žemaitėška)
 * @author Hugo.arg
 */
$messages['bat-smg'] = array(
	'tmh-play' => 'Gruotė',
	'tmh-pause' => 'Pauzė',
	'tmh-stop' => 'Sostabdītė',
	'tmh-play-video' => 'Gruotė video',
	'tmh-play-sound' => 'Gruotė garsa',
	'tmh-download' => 'Atsėsiōstė faila',
);

/** Southern Balochi (بلوچی مکرانی)
 * @author Mostafadaneshvar
 */
$messages['bcc'] = array(
	'tmh-desc' => 'دسگیره په فایلان Ogg Theora و Vorbis, گون پخش کنوک جاوا اسکرسیپت',
	'tmh-short-audio' => 'فایل صوتی Ogg $1، $2',
	'tmh-short-video' => 'فایل تصویری Ogg $1، $2',
	'tmh-short-general' => 'فایل مدیا Ogg $1، $2',
	'tmh-long-audio' => '(اوجی جی  $1 فایل صوتی, طول $2, $3)',
	'tmh-long-video' => '(اوجی جی $1 فایل ویدیو, طول $2, $4×$5 پیکسل, $3)',
	'tmh-long-multiplexed' => '(اوجی جی چند دابی فایل صوت/تصویر, $1, طول $2, $4×$5 پیکسل, $3 کل)',
	'tmh-long-general' => '(اوجی جی فایل مدیا, طول $2, $3)',
	'tmh-long-error' => '(نامعتبرین فایل اوجی جی: $1)',
	'tmh-play' => 'پخش',
	'tmh-pause' => 'توقف',
	'tmh-stop' => 'بند',
	'tmh-play-video' => 'پخش ویدیو',
	'tmh-play-sound' => 'پخش توار',
	'tmh-no-player' => 'شرمنده،شمی سیستم جاه کیت که هچ برنامه حمایتی پخش کنوک نیست.
لطفا <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download"> یک پخش کنوکی ای گیزیت</a>.',
	'tmh-no-xiphqt' => 'چوش جاه کیت که شما را جز XiphQTپه کویک تایم نیست.
کویک تایم بی ای جز نه تونیت فایلان اوجی جی بوانیت.
لطف <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ایرگیزیت XiphQT</a> یا دگه وانوکی انتخاب کنیت.',
	'tmh-player-videoElement' => '<video> جزء',
	'tmh-player-oggPlugin' => ' پلاگین اوجی جی',
	'tmh-player-cortado' => 'کارتادو(جاوا)',
	'tmh-player-vlc-mozilla' => 'وی ال سی',
	'tmh-player-vlc-activex' => 'VLC (ActiveX)وی ال سی',
	'tmh-player-quicktime-mozilla' => 'کویک تایم',
	'tmh-player-quicktime-activex' => 'QuickTime (ActiveX) کویک تایم',
	'tmh-player-thumbnail' => 'هنگت فقط عکس',
	'tmh-player-soundthumb' => 'هچ پخش کنوک',
	'tmh-player-selected' => '(انتخابی)',
	'tmh-use-player' => 'استفاده کن پخش کنوک',
	'tmh-more' => 'گیشتر...',
	'tmh-dismiss' => 'بندگ',
	'tmh-download' => 'ایرگیزگ فایل',
	'tmh-desc-link' => 'ای فایل باره',
);

/** Bikol Central (Bikol Central)
 * @author Filipinayzd
 */
$messages['bcl'] = array(
	'tmh-more' => 'Dakol pa..',
	'tmh-dismiss' => 'Isara',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 * @author Red Winged Duck
 */
$messages['be-tarask'] = array(
	'tmh-desc' => 'Апрацоўшчык файлаў Ogg Theora і Vorbis з прайгравальнікам JavaScript',
	'tmh-short-audio' => 'Аўдыё-файл Ogg $1, $2',
	'tmh-short-video' => 'Відэа-файл у фармаце Ogg $1, $2',
	'tmh-short-general' => 'Мэдыя-файл Ogg $1, $2',
	'tmh-long-audio' => '(аўдыё-файл Ogg $1, даўжыня $2, $3)',
	'tmh-long-video' => '(відэа-файл Ogg $1, даўжыня $2, $4×$5 піксэляў, $3)',
	'tmh-long-multiplexed' => '(мультыплексны аўдыё/відэа-файл Ogg, $1, даўжыня $2, $4×$5 піксэляў, усяго $3)',
	'tmh-long-general' => '(мэдыя-файл Ogg, даўжыня $2, $3)',
	'tmh-long-error' => '(Няслушны файл у фармаце Ogg: $1)',
	'tmh-play' => 'Прайграць',
	'tmh-pause' => 'Паўза',
	'tmh-stop' => 'Спыніць',
	'tmh-play-video' => 'Прайграць відэа',
	'tmh-play-sound' => 'Прайграць аўдыё',
	'tmh-no-player' => 'Прабачце, Ваша сыстэма ня мае неабходнага праграмнага забесьпячэньня для прайграваньня файлаў. Калі ласка, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">загрузіце прайгравальнік</a>.',
	'tmh-no-xiphqt' => 'Адсутнічае кампанэнт  XiphQT для QuickTime.
QuickTime ня можа прайграваць файлы ў фармаце Ogg бяз гэтага кампанэнта.
Калі ласка, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">загрузіце XiphQT</a> альбо выберыце іншы прайгравальнік.',
	'tmh-player-videoElement' => 'Убудаваная падтрымка браўзэра',
	'tmh-player-oggPlugin' => 'Плагін для браўзэра',
	'tmh-player-thumbnail' => 'Толькі нерухомая выява',
	'tmh-player-soundthumb' => 'Няма прайгравальніка',
	'tmh-player-selected' => '(выбраны)',
	'tmh-use-player' => 'Выкарыстоўваць прайгравальнік:',
	'tmh-more' => 'Болей…',
	'tmh-dismiss' => 'Зачыніць',
	'tmh-download' => 'Загрузіць файл',
	'tmh-desc-link' => 'Інфармацыя пра гэты файл',
	'tmh-oggThumb-version' => 'OggHandler патрабуе oggThumb вэрсіі $1 ці больш позьняй.',
	'tmh-oggThumb-failed' => 'oggThumb не атрымалася стварыць мініятуру.',
);

/** Bulgarian (Български)
 * @author Borislav
 * @author DCLXVI
 * @author Spiritia
 */
$messages['bg'] = array(
	'tmh-desc' => 'Приложение за файлове тип Ogg Theora и Vorbis, с плейър на JavaScript',
	'tmh-short-audio' => 'Ogg $1 звуков файл, $2',
	'tmh-short-video' => 'Ogg $1 видео файл, $2',
	'tmh-long-audio' => '(Ogg $1 звуков файл, продължителност $2, $3)',
	'tmh-long-video' => '(Ogg $1 видео файл, продължителност $2, $4×$5 пиксела, $3)',
	'tmh-long-general' => '(Мултимедиен файл в ogg формат с дължина $2, $3)',
	'tmh-long-error' => '(Невалиден ogg файл: $1)',
	'tmh-play' => 'Пускане',
	'tmh-pause' => 'Пауза',
	'tmh-stop' => 'Спиране',
	'tmh-play-video' => 'Пускане на видео',
	'tmh-play-sound' => 'Пускане на звук',
	'tmh-no-player' => 'Съжаляваме, но на вашия компютър изглежда няма някой от поддържаните плейъри.
Моля <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">изтеглете си плейър</a>.',
	'tmh-no-xiphqt' => 'Изглежда нямате инсталиран компонента XiphQT за QuickTime.
Без този компонент, QuickTime не може да пуска файлове във формат Ogg.
Моля, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">свалете си XiphQT</a> или изберете друго приложение.',
	'tmh-player-videoElement' => 'Локална поддръжка от браузъра',
	'tmh-player-oggPlugin' => 'Плъгин към браузъра',
	'tmh-player-thumbnail' => 'Само неподвижни изображения',
	'tmh-player-soundthumb' => 'Няма плеър',
	'tmh-player-selected' => '(избран)',
	'tmh-use-player' => 'Ползване на плеър:',
	'tmh-more' => 'Повече...',
	'tmh-dismiss' => 'Затваряне',
	'tmh-download' => 'Изтегляне на файла',
	'tmh-desc-link' => 'Информация за файла',
);

/** Bengali (বাংলা)
 * @author Bellayet
 * @author Zaheen
 */
$messages['bn'] = array(
	'tmh-short-audio' => 'অগ $1 সাউন্ড ফাইল, $2',
	'tmh-short-video' => 'অগ $1 ভিডিও ফাইল, $2',
	'tmh-short-general' => 'অগ $1 মিডিয়া ফাইল, $2',
	'tmh-long-audio' => '(অগ $1 সাউন্ড ফাইল, দৈর্ঘ্য $2, $3)',
	'tmh-long-video' => '(অগ $1 ভিডিও ফাইল, দৈর্ঘ্য $2, $4×$5 পিক্সেল, $3)',
	'tmh-long-multiplexed' => '(অগ মাল্টিপ্লেক্সকৃত অডিও/ভিডিও ফাইল, $1, দৈর্ঘ্য $2, $4×$5 পিক্সেল, $3 সামগ্রিক)',
	'tmh-long-general' => '(অগ মিডিয়া ফাইল, দৈর্ঘ্য $2, $3)',
	'tmh-long-error' => '(অবৈধ অগ ফাইল: $1)',
	'tmh-play' => 'চালানো হোক',
	'tmh-pause' => 'বিরতি',
	'tmh-stop' => 'বন্ধ',
	'tmh-play-video' => 'ভিডিও চালানো হোক',
	'tmh-play-sound' => 'অডিও চালানো হোক',
	'tmh-no-player' => 'দুঃখিত, আপনার কম্পিউটারে ফাইলটি চালনার জন্য কোন সফটওয়্যার নেই। অনুগ্রহ করে <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">চালনাকারী সফটওয়্যার ডাউনলোড করুন</a>।',
	'tmh-no-xiphqt' => 'আপনার কুইকটাইম সফটওয়্যারটিতে XiphQT উপাদানটি নেই। এই উপাদানটি ছাড়া কুইকটাইম অগ ফাইল চালাতে পারবে না। অনুগ্রহ করে <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT ডাউনলোড করুন</a> অথবা অন্য একটি চালনাকারী সফটওয়্যার ব্যবহার করুন।',
	'tmh-player-videoElement' => 'স্থানীয় ব্রাউজার সাপোর্ট',
	'tmh-player-oggPlugin' => 'ব্রাউজার প্লাগ-ইন',
	'tmh-player-thumbnail' => 'শুধুমাত্র স্থির চিত্র',
	'tmh-player-soundthumb' => 'কোন চালনাকারী সফটওয়্যার নেই',
	'tmh-player-selected' => '(নির্বাচিত)',
	'tmh-use-player' => 'এই চালনাকারী সফটওয়্যার ব্যবহার করুন:',
	'tmh-more' => 'আরও...',
	'tmh-dismiss' => 'বন্ধ করা হোক',
	'tmh-download' => 'ফাইল ডাউনলোড করুন',
	'tmh-desc-link' => 'এই ফাইলের বৃত্তান্ত',
);

/** Breton (Brezhoneg)
 * @author Fohanno
 * @author Fulup
 * @author Y-M D
 */
$messages['br'] = array(
	'tmh-short-audio' => 'Restr son Ogg $1, $2',
	'tmh-short-video' => 'Restr video Ogg $1, $2',
	'tmh-short-general' => 'Restr media Ogg $1, $2',
	'tmh-long-audio' => '(Restr son Ogg $1, pad $2, $3)',
	'tmh-long-video' => '(Restr video Ogg $1, pad $2, $4×$5 piksel, $3)',
	'tmh-long-multiplexed' => '(Restr Ogg klevet/video liesplezhet $1, pad $2, $4×$5 piksel, $3 hollad)',
	'tmh-long-general' => '(Restr media Ogg, pad $2, $3)',
	'tmh-long-error' => '(Restr ogg direizh : $1)',
	'tmh-play' => 'Lenn',
	'tmh-pause' => 'Ehan',
	'tmh-stop' => 'Paouez',
	'tmh-play-video' => 'Lenn ar video',
	'tmh-play-sound' => 'Lenn ar son',
	'tmh-player-videoElement' => 'Skor ar merdeer orin',
	'tmh-player-oggPlugin' => 'Adveziant ar merdeer',
	'tmh-player-thumbnail' => 'Skeudenn statek hepken',
	'tmh-player-soundthumb' => 'Lenner ebet',
	'tmh-player-selected' => '(diuzet)',
	'tmh-use-player' => 'Ober gant al lenner :',
	'tmh-more' => "Muioc'h...",
	'tmh-dismiss' => 'Serriñ',
	'tmh-download' => 'Pellgargañ ar restr',
	'tmh-desc-link' => 'Diwar-benn ar restr-mañ',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'tmh-desc' => 'Upravljač za Ogg Theora i Vorbis datotekem sa JavaScript preglednikom',
	'tmh-short-audio' => 'Ogg $1 zvučna datoteka, $2',
	'tmh-short-video' => 'Ogg $1 video datoteka, $2',
	'tmh-short-general' => 'Ogg $1 medijalna datoteka, $2',
	'tmh-long-audio' => '(Ogg $1 zvučna datoteka, dužina $2, $3)',
	'tmh-long-video' => '(Ogg $1 video datoteka, dužina $2, $4×$5 piksela, $3)',
	'tmh-long-multiplexed' => '(Ogg multipleksna zvučna/video datoteka, $1, dužina $2, $4×$5 piksela, $3 sveukupno)',
	'tmh-long-general' => '(Ogg medijalna datoteka, dužina $2, $3)',
	'tmh-long-error' => '(Nevaljana ogg datoteka: $1)',
	'tmh-play' => 'Pokreni',
	'tmh-pause' => 'Pauza',
	'tmh-stop' => 'Zaustavi',
	'tmh-play-video' => 'Pokreni video',
	'tmh-play-sound' => 'Sviraj zvuk',
	'tmh-no-player' => 'Žao nam je, Vaš sistem izgleda da nema nikakvog podržanog softvera za pregled.
Molimo Vas <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">da skinete preglednik</a>.',
	'tmh-no-xiphqt' => 'Izgleda da nemate XiphQT komponentu za program QuickTime.
QuickTime ne može reproducirati Ogg datoteke bez ove komponente.
Molimo Vas da <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">skinete XiphQT</a> ili da odaberete drugi preglednik.',
	'tmh-player-videoElement' => 'Prirodna podrška preglednika',
	'tmh-player-oggPlugin' => 'Dodatak pregledniku',
	'tmh-player-thumbnail' => 'Samo mirne slike',
	'tmh-player-soundthumb' => 'Nema preglednika',
	'tmh-player-selected' => '(odabrano)',
	'tmh-use-player' => 'Koristi svirač:',
	'tmh-more' => 'Više...',
	'tmh-dismiss' => 'Zatvori',
	'tmh-download' => 'Učitaj datoteku',
	'tmh-desc-link' => 'O ovoj datoteci',
);

/** Catalan (Català)
 * @author Aleator
 * @author Paucabot
 * @author SMP
 * @author Toniher
 * @author Vriullop
 */
$messages['ca'] = array(
	'tmh-desc' => 'Gestor de fitxers Ogg Theora i Vorbis, amb reproductor de Javascript',
	'tmh-short-audio' => "Fitxer OGG d'àudio $1, $2",
	'tmh-short-video' => 'Fitxer OGG de vídeo $1, $2',
	'tmh-short-general' => 'Fitxer multimèdia OGG $1, $2',
	'tmh-long-audio' => '(Ogg $1 fitxer de so, llargada $2, $3)',
	'tmh-long-video' => '(Fitxer OGG de vídeo $1, llargada $2, $4×$5 píxels, $3)',
	'tmh-long-multiplexed' => '(Arxiu àudio/vídeo multiplex, $1, llargada $2, $4×$5 píxels, $3 de mitjana)',
	'tmh-long-general' => '(Fitxer multimèdia OGG, llargada $2, $3)',
	'tmh-long-error' => '(Fitxer OGG invàlid: $1)',
	'tmh-play' => 'Reprodueix',
	'tmh-pause' => 'Pausa',
	'tmh-stop' => 'Atura',
	'tmh-play-video' => 'Reprodueix vídeo',
	'tmh-play-sound' => 'Reprodueix so',
	'tmh-no-player' => 'No teniu instaŀlat cap reproductor acceptat. Podeu <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">descarregar-ne</a> un.',
	'tmh-no-xiphqt' => 'No disposeu del component XiphQT al vostre QuickTime. Aquest component és imprescindible per a que el QuickTime pugui reproduir fitxers OGG. Podeu <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">descarregar-lo</a> o escollir un altre reproductor.',
	'tmh-player-videoElement' => 'Suport natiu del navegador',
	'tmh-player-oggPlugin' => 'Connector del navegador',
	'tmh-player-thumbnail' => 'Només un fotograma',
	'tmh-player-soundthumb' => 'Cap reproductor',
	'tmh-player-selected' => '(seleccionat)',
	'tmh-use-player' => 'Usa el reproductor:',
	'tmh-more' => 'Més...',
	'tmh-dismiss' => 'Tanca',
	'tmh-download' => 'Descarrega el fitxer',
	'tmh-desc-link' => 'Informació del fitxer',
);

/** Czech (Česky)
 * @author Li-sung
 * @author Matěj Grabovský
 * @author Mormegil
 */
$messages['cs'] = array(
	'tmh-desc' => 'Obsluha souborů Ogg Theora a Vorbis s JavaScriptovým přehrávačem',
	'tmh-short-audio' => 'Zvukový soubor ogg $1, $2',
	'tmh-short-video' => 'Videosoubor ogg $1, $2',
	'tmh-short-general' => 'Soubor média ogg $1, $2',
	'tmh-long-audio' => '(Zvukový soubor ogg $1, délka $2, $3)',
	'tmh-long-video' => '(Videosoubor $1, délka $2, $4×$5 pixelů, $3)',
	'tmh-long-multiplexed' => '(Audio/video soubor ogg, $1, délka $2, $4×$5 pixelů, $3)',
	'tmh-long-general' => '(Soubor média ogg, délka $2, $3)',
	'tmh-long-error' => '(Chybný soubor ogg: $1)',
	'tmh-play' => 'Přehrát',
	'tmh-pause' => 'Pozastavit',
	'tmh-stop' => 'Zastavit',
	'tmh-play-video' => 'Přehrát video',
	'tmh-play-sound' => 'Přehrát zvuk',
	'tmh-no-player' => 'Váš systém zřejmě neobsahuje žádný podporovaný přehrávač. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Váš systém zřejmě neobsahuje žádný podporovaný přehrávač. </a>.',
	'tmh-no-xiphqt' => 'Nemáte rozšíření XiphQT pro QuickTime. QuickTime nemůže přehrávat soubory ogg bez tohoto rozšíření. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Stáhněte XiphQT</a> nebo vyberte jiný přehrávač.',
	'tmh-player-videoElement' => 'Vestavěná podpora v prohlížeči',
	'tmh-player-oggPlugin' => 'Zásuvný modul do prohlížeče',
	'tmh-player-thumbnail' => 'Pouze snímek náhledu',
	'tmh-player-soundthumb' => 'Žádný přehrávač',
	'tmh-player-selected' => '(zvoleno)',
	'tmh-use-player' => 'Vyberte přehrávač:',
	'tmh-more' => 'Více...',
	'tmh-dismiss' => 'Zavřít',
	'tmh-download' => 'Stáhnout soubor',
	'tmh-desc-link' => 'O tomto souboru',
	'tmh-oggThumb-version' => 'OggHandler vyžaduje oggThumb verze $1 nebo novější.',
	'tmh-oggThumb-failed' => 'oggThumb nedokázal vytvořit náhled.',
);

/** Danish (Dansk)
 * @author Byrial
 * @author Jon Harald Søby
 */
$messages['da'] = array(
	'tmh-desc' => 'Understøtter Ogg Theora- og Vorbis-filer med en JavaScript-afspiller.',
	'tmh-short-audio' => 'Ogg $1 lydfil, $2',
	'tmh-short-video' => 'Ogg $1 videofil, $2',
	'tmh-short-general' => 'Ogg $1 mediafil, $2',
	'tmh-long-audio' => '(Ogg $1 lydfil, længde $2, $3)',
	'tmh-long-video' => '(Ogg $1 videofil, længde $2, $4×$5 pixel, $3)',
	'tmh-long-multiplexed' => '(Sammensat tmh-lyd- og -videofil, $1, længde $2, $4×$5 pixel, $3 samlet)',
	'tmh-long-general' => '(Ogg mediafil, længde $2, $3)',
	'tmh-long-error' => '(Ugyldig tmh-fil: $1)',
	'tmh-play' => 'Afspil',
	'tmh-pause' => 'Pause',
	'tmh-stop' => 'Stop',
	'tmh-play-video' => 'Afspil video',
	'tmh-play-sound' => 'Afspil lyd',
	'tmh-no-player' => 'Desværre ser det ud til at dit system har nogen understøttede medieafspillere.
<a href="http://mediawiki.org/wiki/Extension:OggHandler/Client_download">Download venligst en afspiller</a>.',
	'tmh-no-xiphqt' => 'Det ser ud til at du ikke har XiphQT-komponenten til QuickTime.
QuickTime kan ikke afspille tmh-file uden denne komponent.
<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Download venligst XiphQT</a> eller vælg en anden afspiller.',
	'tmh-player-videoElement' => 'Indbygget browserunderstøttelse',
	'tmh-player-oggPlugin' => 'Browsertilføjelse',
	'tmh-player-thumbnail' => 'Kun stillbilleder',
	'tmh-player-soundthumb' => 'Ingen afspiller',
	'tmh-player-selected' => '(valgt)',
	'tmh-use-player' => 'Brug afspiller:',
	'tmh-more' => 'Mere...',
	'tmh-dismiss' => 'Luk',
	'tmh-download' => 'Download fil',
	'tmh-desc-link' => 'Om denne fil',
);

/** German (Deutsch)
 * @author Kghbln
 * @author Leithian
 * @author Metalhead64
 * @author MichaelFrey
 * @author Raimond Spekking
 * @author Umherirrender
 */
$messages['de'] = array(
	'tmh-desc' => 'Steuerungsprogramm für Ogg Theora- und Vorbis-Dateien, inklusive einer JavaScript-Abspielsoftware',
	'tmh-short-audio' => 'tmh-$1-Audiodatei, $2',
	'tmh-short-video' => 'tmh-$1-Videodatei, $2',
	'tmh-short-general' => 'tmh-$1-Mediadatei, $2',
	'tmh-long-audio' => '(tmh-$1-Audiodatei, Länge: $2, $3)',
	'tmh-long-video' => '(tmh-$1-Videodatei, Länge: $2, $4×$5 Pixel, $3)',
	'tmh-long-multiplexed' => '(tmh-Audio-/Video-Datei, $1, Länge: $2, $4×$5 Pixel, $3)',
	'tmh-long-general' => '(tmh-Mediadatei, Länge: $2, $3)',
	'tmh-long-error' => '(Ungültige tmh-Datei: $1)',
	'tmh-play' => 'Start',
	'tmh-pause' => 'Pause',
	'tmh-stop' => 'Stopp',
	'tmh-play-video' => 'Video abspielen',
	'tmh-play-sound' => 'Audio abspielen',
	'tmh-no-player' => 'Dein System scheint über keine Abspielsoftware zu verfügen. Bitte installiere <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">eine Abspielsoftware</a>.',
	'tmh-no-xiphqt' => 'Dein System scheint nicht über die XiphQT-Komponente für QuickTime zu verfügen. QuickTime kann ohne diese Komponente keine tmh-Dateien abspielen.Bitte <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">lade XiphQT</a> oder wähle eine andere Abspielsoftware.',
	'tmh-player-videoElement' => 'Vorhandene Browserunterstützung',
	'tmh-player-oggPlugin' => 'Browser-Plugin',
	'tmh-player-thumbnail' => 'nur Vorschaubild',
	'tmh-player-soundthumb' => 'Kein Player',
	'tmh-player-selected' => '(ausgewählt)',
	'tmh-use-player' => 'Abspielsoftware:',
	'tmh-more' => 'Optionen …',
	'tmh-dismiss' => 'Schließen',
	'tmh-download' => 'Datei speichern',
	'tmh-desc-link' => 'Über diese Datei',
	'tmh-oggThumb-version' => 'OggHandler erfordert oggThumb in der Version $1 oder höher.',
	'tmh-oggThumb-failed' => 'oggThumb konnte kein Miniaturbild erstellen.',
);

/** German (formal address) (Deutsch (Sie-Form))
 * @author Raimond Spekking
 * @author Umherirrender
 */
$messages['de-formal'] = array(
	'tmh-no-player' => 'Ihr System scheint über keine Abspielsoftware zu verfügen. Bitte installieren Sie <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">eine Abspielsoftware</a>.',
	'tmh-no-xiphqt' => 'Ihr System scheint nicht über die XiphQT-Komponente für QuickTime zu verfügen. QuickTime kann ohne diese Komponente keine tmh-Dateien abspielen.Bitte <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">laden Sie XiphQT</a> oder wählen Sie eine andere Abspielsoftware.',
);

/** Zazaki (Zazaki)
 * @author Aspar
 * @author Xoser
 */
$messages['diq'] = array(
	'tmh-desc' => 'Qe dosyayanê Ogg Theora u Vorbisî pê JavaScriptî qulp',
	'tmh-short-audio' => 'Ogg $1 dosyaya vengi, $2',
	'tmh-short-video' => 'Ogg $1 dosyaya filmi, $2',
	'tmh-short-general' => 'Ogg $1 dosyaya medyayi, $2',
	'tmh-long-audio' => '(Ogg $1 dosyaya medyayi,  mudde $2, $3)',
	'tmh-long-video' => '(Ogg $1 dosyaya filmi, mudde $2, $4×$5 piksel, $3)',
	'tmh-long-multiplexed' => '(Ogg dosyaya filmi/vengi yo multiexed, $1, mudde $2, $4×$5 piksel, $3 bıumumi)',
	'tmh-long-general' => '(Ogg dosyaya medyayi, mudde $2, $3)',
	'tmh-long-error' => '(dosyaya oggi yo nemeqbul: $1)',
	'tmh-play' => "bıd' kaykerdış",
	'tmh-pause' => 'vındarn',
	'tmh-stop' => 'vındarn',
	'tmh-play-video' => "video bıd' kaykerdış",
	'tmh-play-sound' => "veng bıd' kaykerdış",
	'tmh-no-player' => 'ma meluli, wina aseno ke sistemê şıma wayirê softwareyi yo player niyo.
kerem kerê <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">yew player biyare war</a>.',
	'tmh-no-xiphqt' => 'qey QuickTimeyi wina aseno ke şıma wayirê parçeyê XiphQTi niyê.
heta ke parçeyê QuickTimeyi çinibi dosyayê Oggyi nêxebıtiyeni.
kerem kerê<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT\'i biyar war</a> ya zi yewna player bıvıcinê.',
	'tmh-player-videoElement' => 'destekê cıgêrayoxê mehelliyi',
	'tmh-player-oggPlugin' => 'zeylê cıgêrayoxi',
	'tmh-player-thumbnail' => 'hema têna resm o.',
	'tmh-player-soundthumb' => 'player çino',
	'tmh-player-selected' => '(vıciyaye)',
	'tmh-use-player' => 'player bışuxuln:',
	'tmh-more' => 'hema....',
	'tmh-dismiss' => 'bıqefeln',
	'tmh-download' => 'dosya biyar war',
	'tmh-desc-link' => 'derheqê dosyayi de',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'tmh-desc' => 'Wóźeński program za dataje Ogg Theora a Vprbis z JavaScriptowym wótegrawakom',
	'tmh-short-audio' => 'Ogg $1 awdiodataja, $2',
	'tmh-short-video' => 'Ogg $1 wideodataja, $2',
	'tmh-short-general' => 'Ogg $1 medijowa dataja, $2',
	'tmh-long-audio' => '(Ogg $1 awdiodataja, dłujkosć $2, $3)',
	'tmh-long-video' => '(Ogg $1 wideodataja, dłujkosć $2, $4×$5 pikselow, $3)',
	'tmh-long-multiplexed' => '(ogg multipleksowa awdio-/wideodataja, $1, dłujkosć $2, $4×$5 pikselow, $3 dogromady)',
	'tmh-long-general' => '(Ogg medijowa dataja, dłujkosć $2, $3)',
	'tmh-long-error' => '(Njepłaśiwa tmh-dataja: $1)',
	'tmh-play' => 'Wótegraś',
	'tmh-pause' => 'Pśestank',
	'tmh-stop' => 'Stoj',
	'tmh-play-video' => 'Wideo wótegraś',
	'tmh-play-sound' => 'Zuk wótegraś',
	'tmh-no-player' => 'Wódaj, twój system njezda se pódpěrany wótegrawak měś.
Pšosym <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ześěgni wótegrawak</a>.',
	'tmh-no-xiphqt' => 'Zda se, až njamaš komponentu XiphQT za QuickTime.
QuickTime njamóžo tmh-dataje bźez toś teje komponenty wótegraś.
Pšosym <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Cient_download">ześěgni XiphQT</a> abo wubjeŕ drugi wótegrawak.',
	'tmh-player-videoElement' => 'Zatwarjona pódpěra pśez wobglědowak',
	'tmh-player-oggPlugin' => 'Tykac za wobglědowak',
	'tmh-player-thumbnail' => 'Jano njegibny wobraz',
	'tmh-player-soundthumb' => 'Žeden wótegrawak',
	'tmh-player-selected' => '(wubrany)',
	'tmh-use-player' => 'Wubjeŕ wótgrawak:',
	'tmh-more' => 'Wěcej...',
	'tmh-dismiss' => 'Zacyniś',
	'tmh-download' => 'Dataju ześěgnuś',
	'tmh-desc-link' => 'Wó toś tej dataji',
	'tmh-oggThumb-version' => 'OggHandler trjeba wersiju $1 oggThumb abo nowšu.',
	'tmh-oggThumb-failed' => 'oggThumb njejo mógł wobrazk napóraś.',
);

/** Greek (Ελληνικά)
 * @author Consta
 * @author Dead3y3
 * @author Omnipaedista
 * @author ZaDiak
 */
$messages['el'] = array(
	'tmh-desc' => 'Χειριστής για αρχεία Ogg Theora και Vorbis, με αναπαραγωγέα JavaScript',
	'tmh-short-audio' => 'Αρχείο ήχου Ogg $1, $2',
	'tmh-short-video' => 'Αρχείο βίντεο Ogg $1, $2',
	'tmh-short-general' => 'Αρχείο μέσων Ogg $1, $2',
	'tmh-long-audio' => '(Αρχείο ήχου Ogg $1, διάρκεια $2, $3)',
	'tmh-long-video' => '(Αρχείο βίντεο Ogg $1, διάρκεια $2, $4×$5 pixels, $3)',
	'tmh-long-multiplexed' => '(Αρχείο πολυπλεκτικού ήχου/βίντεο Ogg, $1, διάρκεια $2, $4×$5 pixels, $3 ολικά)',
	'tmh-long-general' => '(Αρχείο μέσων Ogg, διάρκεια $2, $3)',
	'tmh-long-error' => '(Άκυρο αρχείο ogg: $1)',
	'tmh-play' => 'Αναπαραγωγή',
	'tmh-pause' => 'Παύση',
	'tmh-stop' => 'Διακοπή',
	'tmh-play-video' => 'Αναπαραγωγή βίντεο',
	'tmh-play-sound' => 'Αναπαραγωγή ήχου',
	'tmh-no-player' => 'Συγγνώμη, το σύστημά σας δεν φαίνεται να έχει κάποιο υποστηριζόμενο λογισμικό αναπαραγωγής.<br />
Παρακαλώ <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">κατεβάστε ένα πρόγραμμα αναπαραγωγής</a>.',
	'tmh-no-xiphqt' => 'Δεν φαίνεται να έχετε το στοιχείο XiphQT για το πρόγραμμα QuickTime.<br />
Το πρόγραμμα QuickTime δεν μπορεί να αναπαράγει αρχεία Ogg χωρίς αυτό το στοιχείο.<br />
Παρακαλώ <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">κατεβάστε το XiphQT</a> ή επιλέξτε ένα άλλο πρόγραμμα αναπαραγωγής.',
	'tmh-player-videoElement' => 'Τοπική υποστήριξη φυλλομετρητή',
	'tmh-player-oggPlugin' => 'Πρόσθετο φυλλομετρητή',
	'tmh-player-thumbnail' => 'Ακίνητη εικόνα μόνο',
	'tmh-player-soundthumb' => 'Κανένας αναπαραγωγέας',
	'tmh-player-selected' => '(επιλέχθηκε)',
	'tmh-use-player' => 'Χρησιμοποίησε αναπαραγωγέα:',
	'tmh-more' => 'Περισσότερα...',
	'tmh-dismiss' => 'Κλείσιμο',
	'tmh-download' => 'Κατεβάστε το αρχείο',
	'tmh-desc-link' => 'Σχετικά με αυτό το αρχείο',
);

/** Esperanto (Esperanto)
 * @author Amikeco
 * @author ArnoLagrange
 * @author Yekrats
 */
$messages['eo'] = array(
	'tmh-desc' => 'Traktilo por dosieroj Ogg Theora kaj Vobis kun Ĵavaskripta legilo.',
	'tmh-short-audio' => 'Ogg $1 sondosiero, $2',
	'tmh-short-video' => 'Ogg $1 videodosiero, $2',
	'tmh-short-general' => 'Media tmh-dosiero $1, $2',
	'tmh-long-audio' => '(Aŭda tmh-dosiero $1, longeco $2, $3 entute)',
	'tmh-long-video' => '(Video tmh-dosiero $1, longeco $2, $4×$5 pikseloj, $3 entute)',
	'tmh-long-multiplexed' => '(Kunigita aŭdio/video tmh-dosiero, $1, longeco $2, $4×$5 pikseloj, $3 entute)',
	'tmh-long-general' => '(tmh-mediodosiero, longeco $2, $3)',
	'tmh-long-error' => '(Malvalida tmh-dosiero: $1)',
	'tmh-play' => 'Legi',
	'tmh-pause' => 'Paŭzi',
	'tmh-stop' => 'Halti',
	'tmh-play-video' => 'Montri videon',
	'tmh-play-sound' => 'Aŭdigi sonon',
	'tmh-no-player' => 'Ŝajnas ke via sistemo malhavas ian medilegilan programon por legi tian dosieron.
Bonvolu <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">elŝuti iun</a>.',
	'tmh-no-xiphqt' => 'Ŝajnas ke vi malhavas la XiphQT-komponaĵon por QuickTime.
QuickTime ne kapablas aŭdigi sondosierojn sentiu komponaĵo.
Bonvolu <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">elŝuti XiphQT</a> aux elektu alian legilon.',
	'tmh-player-videoElement' => 'Fundamenta subteno per retumilo',
	'tmh-player-oggPlugin' => 'Retumila kromprogramo',
	'tmh-player-thumbnail' => 'Nur senmova bildo',
	'tmh-player-soundthumb' => 'Neniu legilo',
	'tmh-player-selected' => '(elektita)',
	'tmh-use-player' => 'Uzi legilon:',
	'tmh-more' => 'Pli...',
	'tmh-dismiss' => 'Fermi',
	'tmh-download' => 'Alŝuti dosieron',
	'tmh-desc-link' => 'Pri ĉi tiu dosiero',
);

/** Spanish (Español)
 * @author Aleator
 * @author Crazymadlover
 * @author Muro de Aguas
 * @author Remember the dot
 * @author Sanbec
 * @author Spacebirdy
 */
$messages['es'] = array(
	'tmh-desc' => 'Manejador de archivos de Ogg Thedora y Vorbis, con reproductor de JavaScript',
	'tmh-short-audio' => 'Archivo de sonido Ogg $1, $2',
	'tmh-short-video' => 'Archivo de video Ogg $1, $2',
	'tmh-short-general' => 'Archivo Ogg $1, $2',
	'tmh-long-audio' => '(Archivo de sonido Ogg $1, tamaño $2, $3)',
	'tmh-long-video' => '(Archivo de video Ogg $1, tamaño $2, $4×$5 píxeles, $3)',
	'tmh-long-multiplexed' => '(Archivo Ogg de audio/video multiplexado, $1, tamaño $2, $4×$5 píxeles, $3 en todo)',
	'tmh-long-general' => '(Archivo Ogg. tamaño $2, $3)',
	'tmh-long-error' => '(Archivo ogg no válido: $1)',
	'tmh-play' => 'Reproducir',
	'tmh-pause' => 'Pausar',
	'tmh-stop' => 'Detener',
	'tmh-play-video' => 'Reproducir vídeo',
	'tmh-play-sound' => 'Reproducir sonido',
	'tmh-no-player' => 'Lo sentimos, su sistema parece no tener disponible un programa para reproducción de archivos multimedia.
Por favor <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">descargue un reproductor</a>.',
	'tmh-no-xiphqt' => 'Parece que Ud. no tiene el componente XiphQT de QuickTime.
QuckTime no puede reproducir archivos en formato Ogg sin este componente.
Por favor <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">descargue XiphQT</a> o elija otro reproductor de archivos multimedia.',
	'tmh-player-videoElement' => 'Apoyo nativo de navegador',
	'tmh-player-oggPlugin' => 'Complemento de navegador',
	'tmh-player-thumbnail' => 'Únicamente imagen',
	'tmh-player-soundthumb' => 'Ningún reproductor',
	'tmh-player-selected' => '(seleccionado)',
	'tmh-use-player' => 'Usar reproductor:',
	'tmh-more' => 'Opciones...',
	'tmh-dismiss' => 'Cerrar',
	'tmh-download' => 'Bajar archivo',
	'tmh-desc-link' => 'Sobre este archivo',
	'tmh-oggThumb-version' => 'OggHandler requiere una versión oggThumb $1 o posterior.',
	'tmh-oggThumb-failed' => 'oggThumb no pudo crear la imagen miniatura.',
);

/** Estonian (Eesti)
 * @author Avjoska
 * @author Pikne
 * @author Silvar
 */
$messages['et'] = array(
	'tmh-desc' => 'Ogg Theora ja Vorbis failide töötleja JavaScript-esitajaga.',
	'tmh-long-error' => '(Vigane tmh-fail: $1)',
	'tmh-play' => 'Esita',
	'tmh-pause' => 'Paus',
	'tmh-stop' => 'Peata',
	'tmh-play-video' => 'Esita video',
	'tmh-play-sound' => 'Esita heli',
	'tmh-no-player' => 'Kahjuks ei paista su süsteemis olevat ühtki ühilduvat esitustarkvara.
Palun <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">laadi tarkvara alla</a>.',
	'tmh-player-soundthumb' => 'Mängijat ei ole',
	'tmh-player-selected' => '(valitud)',
	'tmh-use-player' => 'Kasuta mängijat:',
	'tmh-more' => 'Lisa...',
	'tmh-dismiss' => 'Sule',
	'tmh-download' => 'Laadi fail alla',
	'tmh-desc-link' => 'Info faili kohta',
);

/** Basque (Euskara)
 * @author An13sa
 * @author Joxemai
 * @author Theklan
 */
$messages['eu'] = array(
	'tmh-desc' => 'Ogg Theora eta Vorbis fitxategientzako edukiontzia, JavaScript playerrarekin',
	'tmh-short-audio' => 'Ogg $1 soinu fitxategia, $2',
	'tmh-short-video' => 'Ogg $1 bideo fitxategia, $2',
	'tmh-short-general' => 'Ogg $1 media fitxategia, $2',
	'tmh-long-audio' => '(Ogg $1 soinu fitxategia, $2 iraupea, $3)',
	'tmh-long-error' => '(ogg fitxategi okerra: $1)',
	'tmh-play' => 'Hasi',
	'tmh-pause' => 'Eten',
	'tmh-stop' => 'Gelditu',
	'tmh-play-video' => 'Bideoa hasi',
	'tmh-play-sound' => 'Soinua hasi',
	'tmh-player-soundthumb' => 'Erreproduktorerik ez',
	'tmh-player-selected' => '(aukeratua)',
	'tmh-use-player' => 'Erabili erreproduktore hau:',
	'tmh-more' => 'Gehiago...',
	'tmh-dismiss' => 'Itxi',
	'tmh-download' => 'Fitxategia jaitsi',
	'tmh-desc-link' => 'Fitxategi honen inguruan',
);

/** Persian (فارسی)
 * @author Huji
 */
$messages['fa'] = array(
	'tmh-desc' => 'به دست گیرندهٔ پرونده‌های Ogg Theora و Vorbis، با پخش‌کنندهٔ مبتنی بر JavaScript',
	'tmh-short-audio' => 'پرونده صوتی Ogg $1، $2',
	'tmh-short-video' => 'پرونده تصویری Ogg $1، $2',
	'tmh-short-general' => 'پرونده Ogg $1، $2',
	'tmh-long-audio' => '(پرونده صوتی Ogg $1، مدت $2، $3)',
	'tmh-long-video' => '(پرونده تصویری Ogg $1، مدت $2 ، $4×$5 پیکسل، $3)',
	'tmh-long-multiplexed' => '(پرونده صوتی/تصویری پیچیده Ogg، $1، مدت $2، $4×$5 پیکسل، $3 در مجموع)',
	'tmh-long-general' => '(پرونده Ogg، مدت $2، $3)',
	'tmh-long-error' => '(پرونده Ogg غیرمجاز: $1)',
	'tmh-play' => 'پخش',
	'tmh-pause' => 'توقف',
	'tmh-stop' => 'قطع',
	'tmh-play-video' => 'پخش تصویر',
	'tmh-play-sound' => 'پخش صوت',
	'tmh-no-player' => 'متاسفانه دستگاه شما نرم‌افزار پخش‌کنندهٔ مناسب ندارد. لطفاً <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">یک برنامهٔ پخش‌کننده بارگیری کنید</a>.',
	'tmh-no-xiphqt' => 'به نظر نمی‌سرد که شما جزء XiphQT از برنامهٔ QuickTime را داشته باشید. برنامهٔ QuickTime بدون این جزء توان پخش پرونده‌های Ogg را ندارد. لطفاً <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT را بارگیری کنید</a> یا از یک پخش‌کنندهٔ دیگر استفاده کنید.',
	'tmh-player-videoElement' => 'پشتیبانی ذاتی مرورگر',
	'tmh-player-oggPlugin' => 'افزونهٔ مرورگر',
	'tmh-player-thumbnail' => 'فقط تصاویر ثابت',
	'tmh-player-soundthumb' => 'فاقد پخش‌کننده',
	'tmh-player-selected' => '(انتخاب شده)',
	'tmh-use-player' => 'استفاده از پخش‌کننده:',
	'tmh-more' => 'بیشتر...',
	'tmh-dismiss' => 'بستن',
	'tmh-download' => 'بارگیری پرونده',
	'tmh-desc-link' => 'دربارهٔ این پرونده',
);

/** Finnish (Suomi)
 * @author Agony
 * @author Crt
 * @author Nike
 * @author Str4nd
 */
$messages['fi'] = array(
	'tmh-desc' => 'Käsittelijä Ogg Theora ja Vorbis -tiedostoille ja JavaScript-soitin.',
	'tmh-short-audio' => 'Ogg $1 -äänitiedosto, $2',
	'tmh-short-video' => 'Ogg $1 -videotiedosto, $2',
	'tmh-short-general' => 'Ogg $1 -mediatiedosto, $2',
	'tmh-long-audio' => '(Ogg $1 -äänitiedosto, $2, $3)',
	'tmh-long-video' => '(Ogg $1 -videotiedosto, $2, $4×$5, $3)',
	'tmh-long-multiplexed' => '(tmh-tiedosto (limitetty kuva ja ääni), $1, $2, $4×$5, $3)',
	'tmh-long-general' => '(tmh-tiedosto, $2, $3)',
	'tmh-long-error' => '(Kelvoton tmh-tiedosto: $1)',
	'tmh-play' => 'Soita',
	'tmh-pause' => 'Tauko',
	'tmh-stop' => 'Pysäytä',
	'tmh-play-video' => 'Toista video',
	'tmh-play-sound' => 'Soita ääni',
	'tmh-no-player' => 'Järjestelmästäsi ei löytynyt mitään tuetuista soitinohjelmista. Voit ladata sopivan <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">soitinohjelman</a>.',
	'tmh-no-xiphqt' => 'Tarvittavaa QuickTimen XiphQT-komponenttia ei löytynyt. QuickTime ei voi toistaa tmh-tiedostoja ilman tätä komponenttia. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Lataa XiphQT</a> tai valitse toinen soitin.',
	'tmh-player-videoElement' => 'Luontainen selaintuki',
	'tmh-player-oggPlugin' => 'Selainlaajennos',
	'tmh-player-thumbnail' => 'Pysäytyskuva',
	'tmh-player-soundthumb' => 'Ei soitinta',
	'tmh-player-selected' => '(valittu)',
	'tmh-use-player' => 'Soitin:',
	'tmh-more' => 'Lisää…',
	'tmh-dismiss' => 'Sulje',
	'tmh-download' => 'Lataa',
	'tmh-desc-link' => 'Tiedoston tiedot',
);

/** Faroese (Føroyskt)
 * @author Spacebirdy
 */
$messages['fo'] = array(
	'tmh-more' => 'Meira...',
);

/** French (Français)
 * @author Crochet.david
 * @author Grondin
 * @author Jean-Frédéric
 * @author Peter17
 * @author Seb35
 * @author Sherbrooke
 * @author Urhixidur
 * @author Verdy p
 */
$messages['fr'] = array(
	'tmh-desc' => 'Support pour les fichiers Ogg Theora et Vorbis, avec un lecteur Javascript',
	'tmh-short-audio' => 'Fichier son Ogg $1, $2',
	'tmh-short-video' => 'Fichier vidéo Ogg $1, $2',
	'tmh-short-general' => 'Fichier média Ogg $1, $2',
	'tmh-long-audio' => '(Fichier son Ogg $1, durée $2, $3)',
	'tmh-long-video' => '(Fichier vidéo Ogg $1, durée $2, $4×$5 pixels, $3)',
	'tmh-long-multiplexed' => '(Fichier multiplexé audio/vidéo Ogg, $1, durée $2, $4×$5 pixels, $3)',
	'tmh-long-general' => '(Fichier média Ogg, durée $2, $3)',
	'tmh-long-error' => '(Fichier Ogg invalide : $1)',
	'tmh-play' => 'Lecture',
	'tmh-pause' => 'Pause',
	'tmh-stop' => 'Arrêt',
	'tmh-play-video' => 'Lire la vidéo',
	'tmh-play-sound' => 'Lire le son',
	'tmh-no-player' => 'Désolé, votre système ne possède apparemment aucun des lecteurs supportés. Veuillez installer <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/fr">un des lecteurs supportés</a>.',
	'tmh-no-xiphqt' => 'Vous n’avez apparemment pas le composant XiphQT pour Quicktime. Quicktime ne peut pas lire les fichiers Ogg sans ce composant. Veuillez <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/fr">télécharger XiphQT</a> ou choisir un autre lecteur.',
	'tmh-player-videoElement' => 'Support du navigateur natif',
	'tmh-player-oggPlugin' => 'Module complémentaire du navigateur',
	'tmh-player-thumbnail' => 'Image statique seulement',
	'tmh-player-soundthumb' => 'Aucun lecteur',
	'tmh-player-selected' => '(sélectionné)',
	'tmh-use-player' => 'Utiliser le lecteur :',
	'tmh-more' => 'Plus…',
	'tmh-dismiss' => 'Fermer',
	'tmh-download' => 'Télécharger le fichier',
	'tmh-desc-link' => 'À propos de ce fichier',
	'tmh-oggThumb-version' => 'OggHandler nécessite oggThumb, version $1 ou supérieure.',
	'tmh-oggThumb-failed' => 'oggThumb n’a pas réussi à créer la miniature.',
);

/** Franco-Provençal (Arpetan)
 * @author ChrisPtDe
 */
$messages['frp'] = array(
	'tmh-desc' => 'Assistance por los fichiérs Ogg Theora et Vorbis, avouéc un liésor JavaScript.',
	'tmh-short-audio' => 'Fichiér son Ogg $1, $2',
	'tmh-short-video' => 'Fichiér vidèô Ogg $1, $2',
	'tmh-short-general' => 'Fichiér multimèdia Ogg $1, $2',
	'tmh-long-audio' => '(Fichiér son Ogg $1, temps $2, $3)',
	'tmh-long-video' => '(Fichiér vidèô Ogg $1, temps $2, $4×$5 pixèls, $3)',
	'tmh-long-multiplexed' => '(Fichiér multiplèxo ôdiô / vidèô Ogg, $1, temps $2, $4×$5 pixèls, $3)',
	'tmh-long-general' => '(Fichiér multimèdia Ogg, temps $2, $3)',
	'tmh-long-error' => '(Fichiér Ogg envalido : $1)',
	'tmh-play' => 'Liére',
	'tmh-pause' => 'Pousa',
	'tmh-stop' => 'Arrét',
	'tmh-play-video' => 'Liére la vidèô',
	'tmh-play-sound' => 'Liére lo son',
	'tmh-no-player' => 'Dèsolâ, aparament voutron sistèmo at gins de liésor recognu.
Volyéd enstalar <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/fr">yon des liésors recognus</a>.',
	'tmh-no-xiphqt' => 'Aparament vos avéd pas lo composent XiphQT por QuickTime.
QuickTime pôt pas liére los fichiérs Ogg sen cél composent.
Volyéd <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/fr">tèlèchargiér XiphQT</a> ou ben chouèsir un ôtro liésor.',
	'tmh-player-videoElement' => 'Assistance du navigator nativa',
	'tmh-player-oggPlugin' => 'Modulo d’èxtension du navigator',
	'tmh-player-thumbnail' => 'Ren que l’émâge fixa',
	'tmh-player-soundthumb' => 'Gins de liésor',
	'tmh-player-selected' => '(chouèsi)',
	'tmh-use-player' => 'Utilisar lo liésor :',
	'tmh-more' => 'De ples...',
	'tmh-dismiss' => 'Cllôre',
	'tmh-download' => 'Tèlèchargiér lo fichiér',
	'tmh-desc-link' => 'A propôs de ceti fichiér',
);

/** Friulian (Furlan)
 * @author Klenje
 */
$messages['fur'] = array(
	'tmh-desc' => 'Gjestôr pai files Ogg Theora e Vorbis, cuntun riprodutôr JavaScript',
	'tmh-short-audio' => 'File audio Ogg $1, $2',
	'tmh-short-video' => 'File video Ogg $1, $2',
	'tmh-short-general' => 'File multimediâl Ogg $1, $2',
	'tmh-long-audio' => '(File audio Ogg $1, durade $2, $3)',
	'tmh-long-video' => '(File video Ogg $1, durade $2, dimensions $4×$5 pixels, $3)',
	'tmh-long-multiplexed' => '(File audio/video multiplexed Ogg $1, lungjece $2, dimensions $4×$5 pixels, in dut $3)',
	'tmh-long-general' => '(File multimediâl Ogg, durade $2, $3)',
	'tmh-long-error' => '(File ogg no valit: $1)',
	'tmh-play' => 'Riprodûs',
	'tmh-pause' => 'Pause',
	'tmh-stop' => 'Ferme',
	'tmh-play-video' => 'Riprodûs il video',
	'tmh-play-sound' => 'Riprodûs il file audio',
	'tmh-no-player' => 'Nus displâs ma il to sisteme nol à riprodutôrs software supuartâts.
Par plasê <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">discjame un riprodutôr</a>.',
	'tmh-no-xiphqt' => 'Al samee che no tu vedis il component XiphQT par QuickTime.
QuickTime nol pues riprodusi i files Ogg cence di chest component.
Par plasê <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">discjame XiphQT</a> o sielç un altri letôr.',
	'tmh-player-videoElement' => 'Supuart sgarfadôr natîf',
	'tmh-player-oggPlugin' => 'Plugin sgarfadôr',
	'tmh-player-thumbnail' => 'Dome figure fisse',
	'tmh-player-soundthumb' => 'Nissun riprodutôr',
	'tmh-player-selected' => '(selezionât)',
	'tmh-use-player' => 'Dopre il riprodutôr:',
	'tmh-more' => 'Altri...',
	'tmh-dismiss' => 'Siere',
	'tmh-download' => 'Discjame il file',
	'tmh-desc-link' => 'Informazions su chest file',
);

/** Irish (Gaeilge)
 * @author Spacebirdy
 */
$messages['ga'] = array(
	'tmh-dismiss' => 'Dún',
);

/** Galician (Galego)
 * @author Toliño
 * @author Xosé
 */
$messages['gl'] = array(
	'tmh-desc' => 'Manipulador dos ficheiros Ogg Theora e mais dos ficheiros Vorbis co reprodutor JavaScript',
	'tmh-short-audio' => 'Ficheiro de son Ogg $1, $2',
	'tmh-short-video' => 'Ficheiro de vídeo Ogg $1, $2',
	'tmh-short-general' => 'Ficheiro multimedia Ogg $1, $2',
	'tmh-long-audio' => '(Ficheiro de son Ogg $1, duración $2, $3)',
	'tmh-long-video' => '(Ficheiro de vídeo Ogg $1, duración $2, $4×$5 píxeles, $3)',
	'tmh-long-multiplexed' => '(Ficheiro de son/vídeo Ogg multiplex, $1, duración $2, $4×$5 píxeles, $3 total)',
	'tmh-long-general' => '(Ficheiro multimedia Ogg, duración $2, $3)',
	'tmh-long-error' => '(Ficheiro Ogg non válido: $1)',
	'tmh-play' => 'Reproducir',
	'tmh-pause' => 'Pausar',
	'tmh-stop' => 'Deter',
	'tmh-play-video' => 'Reproducir o vídeo',
	'tmh-play-sound' => 'Reproducir o son',
	'tmh-no-player' => 'Parece que o seu sistema non dispón do software de reprodución axeitado.
<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Instale un reprodutor</a>.',
	'tmh-no-xiphqt' => 'Parece que non dispón do compoñente XiphQT para QuickTime. QuickTime non pode reproducir ficheiros Ogg sen este componente. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Instale XiphQT</a> ou escolla outro reprodutor.',
	'tmh-player-videoElement' => 'Soporte do navegador nativo',
	'tmh-player-oggPlugin' => 'Complemento do navegador',
	'tmh-player-thumbnail' => 'Só instantánea',
	'tmh-player-soundthumb' => 'Ningún reprodutor',
	'tmh-player-selected' => '(seleccionado)',
	'tmh-use-player' => 'Usar o reprodutor:',
	'tmh-more' => 'Máis...',
	'tmh-dismiss' => 'Fechar',
	'tmh-download' => 'Descargar o ficheiro',
	'tmh-desc-link' => 'Acerca deste ficheiro',
	'tmh-oggThumb-version' => 'O OggHandler necesita a versión $1 ou unha posterior do oggThumb.',
	'tmh-oggThumb-failed' => 'Houbo un erro por parte do oggThumb ao crear a miniatura.',
);

/** Ancient Greek (Ἀρχαία ἑλληνικὴ)
 * @author Crazymadlover
 * @author Flyax
 * @author Omnipaedista
 */
$messages['grc'] = array(
	'tmh-long-error' => '(Ἄκυρα ἀρχεῖα ogg: $1)',
	'tmh-play' => 'Ἀναπαράγειν',
	'tmh-player-selected' => '(ἐπειλεγμένη)',
	'tmh-more' => 'πλέον...',
	'tmh-dismiss' => 'Κλῄειν',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 * @author Melancholie
 */
$messages['gsw'] = array(
	'tmh-desc' => 'Styyrigsprogramm fir Ogg Theora- un Vorbis-Dateie, mit ere JavaScript-Abspiilsoftware',
	'tmh-short-audio' => 'tmh-$1-Audiodatei, $2',
	'tmh-short-video' => 'tmh-$1-Videodatei, $2',
	'tmh-short-general' => 'tmh-$1-Mediadatei, $2',
	'tmh-long-audio' => '(tmh-$1-Audiodatei, Längi: $2, $3)',
	'tmh-long-video' => '(tmh-$1-Videodatei, Längi: $2, $4×$5 Pixel, $3)',
	'tmh-long-multiplexed' => '(tmh-Audio-/Video-Datei, $1, Längi: $2, $4×$5 Pixel, $3)',
	'tmh-long-general' => '(tmh-Mediadatei, Längi: $2, $3)',
	'tmh-long-error' => '(Uugiltigi tmh-Datei: $1)',
	'tmh-play' => 'Start',
	'tmh-pause' => 'Paus',
	'tmh-stop' => 'Stopp',
	'tmh-play-video' => 'Video abspiile',
	'tmh-play-sound' => 'Audio abspiile',
	'tmh-no-player' => 'Dyy Syschtem het schyyns kei Abspiilsoftware. Bitte installier <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">e Abspiilsoftware</a>.',
	'tmh-no-xiphqt' => 'Dyy Syschtem het schyyns d XiphQT-Komponent fir QuickTime nit. QuickTime cha ohni die Komponent kei tmh-Dateie abspiile. Bitte <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">lad XiphQT</a> oder wehl e anderi Abspiilsoftware.',
	'tmh-player-videoElement' => 'Vorhandeni Browserunterstitzig',
	'tmh-player-oggPlugin' => 'Browser-Plugin',
	'tmh-player-thumbnail' => 'Zeig Vorschaubild',
	'tmh-player-soundthumb' => 'Kei Player',
	'tmh-player-selected' => '(usgwehlt)',
	'tmh-use-player' => 'Abspiilsoftware:',
	'tmh-more' => 'Meh …',
	'tmh-dismiss' => 'Zuemache',
	'tmh-download' => 'Datei spychere',
	'tmh-desc-link' => 'Iber die Datei',
);

/** Manx (Gaelg)
 * @author MacTire02
 */
$messages['gv'] = array(
	'tmh-desc-link' => 'Mychione y choadan shoh',
);

/** Hebrew (עברית)
 * @author Rotem Liss
 * @author Rotemliss
 * @author YaronSh
 */
$messages['he'] = array(
	'tmh-desc' => 'מציג מדיה לקובצי Ogg Theora ו־Vorbis, עם נגן JavaScript',
	'tmh-short-audio' => 'קובץ שמע $1 של Ogg, $2',
	'tmh-short-video' => 'קובץ וידאו $1 של Ogg, $2',
	'tmh-short-general' => 'קובץ מדיה $1 של Ogg, $2',
	'tmh-long-audio' => '(קובץ שמע $1 של Ogg, באורך $2, $3)',
	'tmh-long-video' => '(קובץ וידאו $1 של Ogg, באורך $2, $4×$5 פיקסלים, $3)',
	'tmh-long-multiplexed' => '(קובץ מורכב של שמע/וידאו בפורמט Ogg, $1, באורך $2, $4×$5 פיקסלים, $3 בסך הכל)',
	'tmh-long-general' => '(קובץ מדיה של Ogg, באורך $2, $3)',
	'tmh-long-error' => '(קובץ ogg בלתי תקין: $1)',
	'tmh-play' => 'נגן',
	'tmh-pause' => 'הפסק',
	'tmh-stop' => 'עצור',
	'tmh-play-video' => 'נגן וידאו',
	'tmh-play-sound' => 'נגן שמע',
	'tmh-no-player' => 'מצטערים, נראה שהמערכת שלכם אינה כוללת תוכנת נגן נתמכת. אנא <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">הורידו נגן</a>.',
	'tmh-no-xiphqt' => 'נראה שלא התקנתם את רכיב XiphQT של QuickTime, אך QuickTime אינו יכול לנגן קובצי Ogg בלי רכיב זה. אנא <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">הורידו את XiphQT</a> או בחרו נגן אחר.',
	'tmh-player-videoElement' => 'תמיכה טבעית של הדפדפן',
	'tmh-player-oggPlugin' => 'תוסף לדפדפן',
	'tmh-player-thumbnail' => 'עדיין תמונה בלבד',
	'tmh-player-soundthumb' => 'אין נגן',
	'tmh-player-selected' => '(נבחר)',
	'tmh-use-player' => 'שימוש בנגן:',
	'tmh-more' => 'עוד…',
	'tmh-dismiss' => 'סגירה',
	'tmh-download' => 'הורדת הקובץ',
	'tmh-desc-link' => 'אודות הקובץ',
);

/** Hindi (हिन्दी)
 * @author Kaustubh
 * @author Shyam
 */
$messages['hi'] = array(
	'tmh-desc' => 'ऑग थियोरा और वॉर्बिस फ़ाईल्सके लिये चालक, जावास्क्रीप्ट प्लेयर के साथ',
	'tmh-short-audio' => 'ऑग $1 ध्वनी फ़ाईल, $2',
	'tmh-short-video' => 'ऑग $1 चलतचित्र फ़ाईल, $2',
	'tmh-short-general' => 'ऑग $1 मीडिया फ़ाईल, $2',
	'tmh-long-audio' => '(ऑग $1 ध्वनी फ़ाईल, लंबाई $2, $3)',
	'tmh-long-video' => '(ऑग $1 चलतचित्र फ़ाईल, लंबाई $2, $4×$5 पीक्सेल्स, $3)',
	'tmh-long-multiplexed' => '(ऑग ध्वनी/चित्र फ़ाईल, $1, लंबाई $2, $4×$5 पिक्सेल्स, $3 कुल)',
	'tmh-long-general' => '(ऑग मीडिया फ़ाईल, लंबाई $2, $3)',
	'tmh-long-error' => '(गलत ऑग फ़ाईल: $1)',
	'tmh-play' => 'शुरू करें',
	'tmh-pause' => 'विराम',
	'tmh-stop' => 'रोकें',
	'tmh-play-video' => 'विडियो शुरू करें',
	'tmh-play-sound' => 'ध्वनी चलायें',
	'tmh-no-player' => 'क्षमा करें, आपके तंत्र में कोई प्रमाणिक चालक सॉफ्टवेयर दर्शित नहीं हो रहा है।
कृपया <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">एक चालक डाउनलोड करें</a>।',
	'tmh-no-xiphqt' => 'आपके पास QuickTime के लिए XiphQT घटक प्रतीत नहीं हो रहा है।
QuickTime बिना इस घटक के Ogg files चलने में असमर्थ है।
कृपया <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT डाउनलोड करें</a> अथवा अन्य चालक चुनें।',
	'tmh-player-videoElement' => '<video> घटक',
	'tmh-player-oggPlugin' => 'ऑग प्लगीन',
	'tmh-player-thumbnail' => 'सिर्फ स्थिर चित्र',
	'tmh-player-soundthumb' => 'प्लेअर नहीं हैं',
	'tmh-player-selected' => '(चुने हुए)',
	'tmh-use-player' => 'यह प्लेअर इस्तेमाल करें:',
	'tmh-more' => 'और...',
	'tmh-dismiss' => 'बंद करें',
	'tmh-download' => 'फ़ाईल डाउनलोड करें',
	'tmh-desc-link' => 'इस फ़ाईलके बारे में',
);

/** Croatian (Hrvatski)
 * @author CERminator
 * @author Dalibor Bosits
 * @author Ex13
 * @author SpeedyGonsales
 */
$messages['hr'] = array(
	'tmh-desc' => 'Poslužitelj za Ogg Theora i Vorbis datoteke, s JavaScript preglednikom',
	'tmh-short-audio' => 'Ogg $1 zvučna datoteka, $2',
	'tmh-short-video' => 'Ogg $1 video datoteka, $2',
	'tmh-short-general' => 'Ogg $1 medijska datoteka, $2',
	'tmh-long-audio' => '(Ogg $1 zvučna datoteka, duljine $2, $3)',
	'tmh-long-video' => '(Ogg $1 video datoteka, duljine $2, $4x$5 piksela, $3)',
	'tmh-long-multiplexed' => '(Ogg multipleksirana zvučna/video datoteka, $1, duljine $2, $4×$5 piksela, $3 ukupno)',
	'tmh-long-general' => '(Ogg medijska datoteka, duljine $2, $3)',
	'tmh-long-error' => '(nevaljana ogg datoteka: $1)',
	'tmh-play' => 'Pokreni',
	'tmh-pause' => 'Pauziraj',
	'tmh-stop' => 'Zaustavi',
	'tmh-play-video' => 'Pokreni video',
	'tmh-play-sound' => 'Sviraj zvuk',
	'tmh-no-player' => "Oprostite, izgleda da Vaš operacijski sustav nema instalirane medijske preglednike. Molimo <a href=\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\">instalirajte medijski preglednik (''player'')</a>.",
	'tmh-no-xiphqt' => "Nemate instaliranu XiphQT komponentu za QuickTime (ili je neispravno instalirana). QuickTime ne može pokretati Ogg datoteke bez ove komponente. Molimo <a href=\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\">instalirajte XiphQT</a> ili izaberite drugi preglednik (''player'').",
	'tmh-player-videoElement' => 'Ugrađena podrška za preglednik',
	'tmh-player-oggPlugin' => 'Plugin preglednika',
	'tmh-player-vlc-activex' => 'VLC (ActiveX kontrola)',
	'tmh-player-thumbnail' => 'Samo (nepokretne) slike',
	'tmh-player-soundthumb' => 'Nema preglednika',
	'tmh-player-selected' => '(odabran)',
	'tmh-use-player' => "Rabi preglednik (''player''):",
	'tmh-more' => 'Više...',
	'tmh-dismiss' => 'Zatvori',
	'tmh-download' => 'Snimi datoteku',
	'tmh-desc-link' => 'O ovoj datoteci',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Dundak
 * @author Michawiki
 */
$messages['hsb'] = array(
	'tmh-desc' => 'Wodźenski program za dataje Ogg Theora a Vorbis, z JavaScriptowym wothrawakom',
	'tmh-short-audio' => 'Awdiodataja Ogg $1, $2',
	'tmh-short-video' => 'Widejodataja Ogg $1, $2',
	'tmh-short-general' => 'Ogg medijowa dataja $1, $2',
	'tmh-long-audio' => '(tmh-awdiodataja $1, dołhosć: $2, $3)',
	'tmh-long-video' => '(tmh-widejodataja $1, dołhosć: $2, $4×$5 pikselow, $3)',
	'tmh-long-multiplexed' => '(Ogg multipleksna awdio-/widejodataja, $1, dołhosć: $2, $4×$5 pikselow, $3)',
	'tmh-long-general' => '(Ogg medijowa dataja, dołhosć: $2, $3)',
	'tmh-long-error' => '(Njepłaćiwa tmh-dataja: $1)',
	'tmh-play' => 'Wothrać',
	'tmh-pause' => 'Přestawka',
	'tmh-stop' => 'Stój',
	'tmh-play-video' => 'Widejo wothrać',
	'tmh-play-sound' => 'Zynk wothrać',
	'tmh-no-player' => 'Bohužel twój system po wšěm zdaću nima wothrawansku software. Prošu <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">sćehń wothrawak</a>.',
	'tmh-no-xiphqt' => 'Po wšěm zdaću nimaš komponentu XiphQT za QuickTime. QuickTime njemóže tmh-dataje bjez tuteje komponenty wothrawać. Prošu <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">sćehń XiphQT</a> abo wubjer druhi wothrawak.',
	'tmh-player-videoElement' => 'Element <video>',
	'tmh-player-oggPlugin' => 'Tykač Ogg',
	'tmh-player-thumbnail' => 'Napohlad pokazać',
	'tmh-player-soundthumb' => 'Žadyn wothrawak',
	'tmh-player-selected' => '(wubrany)',
	'tmh-use-player' => 'Wothrawak wubrać:',
	'tmh-more' => 'Wjace ...',
	'tmh-dismiss' => 'Začinić',
	'tmh-download' => 'Dataju sćahnyć',
	'tmh-desc-link' => 'Wo tutej dataji',
	'tmh-oggThumb-version' => 'OggHandler trjeba wersiju $1 oggThumb abo nowšu.',
	'tmh-oggThumb-failed' => 'oggThumb njemóžeše wobrazk wutworić.',
);

/** Haitian (Kreyòl ayisyen)
 * @author Masterches
 */
$messages['ht'] = array(
	'tmh-play' => 'Jwe',
	'tmh-pause' => 'Poz',
	'tmh-stop' => 'Stope',
);

/** Hungarian (Magyar)
 * @author Dani
 * @author Tgr
 */
$messages['hu'] = array(
	'tmh-desc' => 'JavaScript nyelven írt lejátszó Ogg Theora és Vorbis fájlokhoz',
	'tmh-short-audio' => 'Ogg $1 hangfájl, $2',
	'tmh-short-video' => 'Ogg $1 videofájl, $2',
	'tmh-short-general' => 'Ogg $1 médiafájl, $2',
	'tmh-long-audio' => '(Ogg $1 hangfájl, hossza: $2, $3)',
	'tmh-long-video' => '(Ogg $1 videófájl, hossza $2, $4×$5 képpont, $3)',
	'tmh-long-multiplexed' => '(Ogg egyesített audió- és videófájl, $1, hossz: $2, $4×$5 képpont, $3 összesen)',
	'tmh-long-general' => '(Ogg médiafájl, hossza: $2, $3)',
	'tmh-long-error' => '(Érvénytelen ogg fájl: $1)',
	'tmh-play' => 'Lejátszás',
	'tmh-pause' => 'Szüneteltetés',
	'tmh-stop' => 'Állj',
	'tmh-play-video' => 'Videó lejátszása',
	'tmh-play-sound' => 'Hang lejátszása',
	'tmh-no-player' => 'Sajnáljuk, de úgy tűnik, hogy nem rendelkezel a megfelelő lejátszóval. Amennyiben le szeretnéd játszani, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">tölts le egyet</a>.',
	'tmh-no-xiphqt' => 'Úgy tűnik, nem rendelkezel a QuickTime-hoz való XiphQT összetevővel. Enélkül a QuickTime nem tudja lejátszani az Ogg fájlokat. A lejátszáshoz tölts le egyet <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">innen</a>, vagy válassz másik lejátszót.',
	'tmh-player-videoElement' => 'A böngésző támogatja',
	'tmh-player-oggPlugin' => 'Beépülő modul böngészőhöz',
	'tmh-player-thumbnail' => 'Csak állókép',
	'tmh-player-soundthumb' => 'Nincs lejátszó',
	'tmh-player-selected' => '(kiválasztott)',
	'tmh-use-player' => 'Lejátszó:',
	'tmh-more' => 'Tovább...',
	'tmh-dismiss' => 'Bezárás',
	'tmh-download' => 'Fájl letöltése',
	'tmh-desc-link' => 'Fájlinformációk',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'tmh-desc' => 'Gestor pro le files Ogg Theora e Vorbis, con reproductor JavaScript',
	'tmh-short-audio' => 'File audio Ogg $1, $2',
	'tmh-short-video' => 'File video Ogg $1, $2',
	'tmh-short-general' => 'File media Ogg $1, $2',
	'tmh-long-audio' => '(File audio Ogg $1, duration $2, $3)',
	'tmh-long-video' => '(File video Ogg $1, duration $2, $4×$5 pixel, $3)',
	'tmh-long-multiplexed' => '(File multiplexate audio/video Ogg, $1, duration $2, $4×$5 pixel, $3 in total)',
	'tmh-long-general' => '(File media Ogg, duration $2, $3)',
	'tmh-long-error' => '(File Ogg invalide: $1)',
	'tmh-play' => 'Jocar',
	'tmh-pause' => 'Pausar',
	'tmh-stop' => 'Stoppar',
	'tmh-play-video' => 'Jocar video',
	'tmh-play-sound' => 'Sonar audio',
	'tmh-no-player' => 'Excusa, ma il pare que non es installate alcun lector compatibile in tu systema.
Per favor <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">discarga un lector.</a>',
	'tmh-no-xiphqt' => 'Pare que tu non ha le componente XiphQT pro QuickTime.
Sin iste componente, QuickTime non sape leger le files Ogg.
Per favor <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">discarga XiphQT</a> o selige un altere lector.',
	'tmh-player-videoElement' => 'Supporto native in navigator',
	'tmh-player-oggPlugin' => 'Plugin pro navigator',
	'tmh-player-thumbnail' => 'Imagine static solmente',
	'tmh-player-soundthumb' => 'Necun lector',
	'tmh-player-selected' => '(seligite)',
	'tmh-use-player' => 'Usar lector:',
	'tmh-more' => 'Plus…',
	'tmh-dismiss' => 'Clauder',
	'tmh-download' => 'Discargar file',
	'tmh-desc-link' => 'A proposito de iste file',
	'tmh-oggThumb-version' => 'OggHandler require oggThumb version $1 o plus recente.',
	'tmh-oggThumb-failed' => 'oggThumb ha fallite de crear le miniatura.',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 * @author Irwangatot
 * @author IvanLanin
 * @author Rex
 */
$messages['id'] = array(
	'tmh-desc' => 'Menangani berkas Ogg Theora dan Vorbis dengan pemutar JavaScript',
	'tmh-short-audio' => 'Berkas suara $1 ogg, $2',
	'tmh-short-video' => 'Berkas video $1 ogg, $2',
	'tmh-short-general' => 'Berkas media $1 ogg, $2',
	'tmh-long-audio' => '(Berkas suara $1 ogg, panjang $2, $3)',
	'tmh-long-video' => '(Berkas video $1 ogg, panjang $2, $4×$5 piksel, $3)',
	'tmh-long-multiplexed' => '(Berkas audio/video multiplexed ogg, $1, panjang $2, $4×$5 piksel, $3 keseluruhan)',
	'tmh-long-general' => '(Berkas media ogg, panjang $2, $3)',
	'tmh-long-error' => '(Berkas ogg tak valid: $1)',
	'tmh-play' => 'Mainkan',
	'tmh-pause' => 'Jeda',
	'tmh-stop' => 'Berhenti',
	'tmh-play-video' => 'Putar video',
	'tmh-play-sound' => 'Putar suara',
	'tmh-no-player' => 'Maaf, sistem Anda tampaknya tak memiliki satupun perangkat lunak pemutar yang mendukung.
Silakan <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">mengunduh salah satu pemutar</a>.',
	'tmh-no-xiphqt' => 'Tampaknya Anda tak memiliki komponen XiphQT untuk QuickTime. QuickTime tak dapat memutar berkas Ogg tanpa komponen ini. Silakan <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">mengunduh XiphQT</a> atau pilih pemutar lain.',
	'tmh-player-videoElement' => 'elemen <video>',
	'tmh-player-oggPlugin' => 'plugin Ogg',
	'tmh-player-thumbnail' => 'Hanya gambar statis',
	'tmh-player-soundthumb' => 'Tak ada pemutar',
	'tmh-player-selected' => '(terpilih)',
	'tmh-use-player' => 'Gunakan pemutar:',
	'tmh-more' => 'Lainnya...',
	'tmh-dismiss' => 'Tutup',
	'tmh-download' => 'Unduh berkas',
	'tmh-desc-link' => 'Mengenai berkas ini',
);

/** Ido (Ido)
 * @author Malafaya
 */
$messages['io'] = array(
	'tmh-long-error' => '(Ne-valida tmh-arkivo: $1)',
	'tmh-player-selected' => '(selektita)',
	'tmh-more' => 'Plus…',
	'tmh-dismiss' => 'Klozar',
	'tmh-desc-link' => 'Pri ca arkivo',
);

/** Icelandic (Íslenska)
 * @author S.Örvarr.S
 * @author Spacebirdy
 */
$messages['is'] = array(
	'tmh-play' => 'Spila',
	'tmh-pause' => 'gera hlé',
	'tmh-stop' => 'Stöðva',
	'tmh-play-video' => 'Spila myndband',
	'tmh-play-sound' => 'Spila hljóð',
	'tmh-player-soundthumb' => 'Enginn spilari',
	'tmh-player-selected' => '(valið)',
	'tmh-use-player' => 'Nota spilara:',
	'tmh-more' => 'Meira...',
	'tmh-dismiss' => 'Loka',
	'tmh-download' => 'Sækja skrá',
);

/** Italian (Italiano)
 * @author .anaconda
 * @author BrokenArrow
 * @author Darth Kule
 */
$messages['it'] = array(
	'tmh-desc' => 'Gestore per i file Ogg Theora e Vorbis, con programma di riproduzione in JavaScript',
	'tmh-short-audio' => 'File audio Ogg $1, $2',
	'tmh-short-video' => 'File video Ogg $1, $2',
	'tmh-short-general' => 'File multimediale Ogg $1, $2',
	'tmh-long-audio' => '(File audio Ogg $1, durata $2, $3)',
	'tmh-long-video' => '(File video Ogg $1, durata $2, dimensioni $4×$5 pixel, $3)',
	'tmh-long-multiplexed' => '(File audio/video multiplexed Ogg $1, durata $2, dimensioni $4×$5 pixel, complessivamente $3)',
	'tmh-long-general' => '(File multimediale Ogg, durata $2, $3)',
	'tmh-long-error' => '(File ogg non valido: $1)',
	'tmh-play' => 'Riproduci',
	'tmh-pause' => 'Pausa',
	'tmh-stop' => 'Ferma',
	'tmh-play-video' => 'Riproduci il filmato',
	'tmh-play-sound' => 'Riproduci il file sonoro',
	'tmh-no-player' => 'Siamo spiacenti, ma non risulta installato alcun software di riproduzione compatibile. Si prega di <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">scaricare un lettore</a> adatto.',
	'tmh-no-xiphqt' => 'Non risulta installato il componente XiphQT di QuickTime. Senza tale componente non è possibile la riproduzione di file Ogg con QuickTime. Si prega di <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">scaricare XiphQT</a> o scegliere un altro lettore.',
	'tmh-player-videoElement' => 'Supporto browser nativo',
	'tmh-player-oggPlugin' => 'Plugin browser',
	'tmh-player-thumbnail' => 'Solo immagini fisse',
	'tmh-player-soundthumb' => 'Nessun lettore',
	'tmh-player-selected' => '(selezionato)',
	'tmh-use-player' => 'Usa il lettore:',
	'tmh-more' => 'Altro...',
	'tmh-dismiss' => 'Chiudi',
	'tmh-download' => 'Scarica il file',
	'tmh-desc-link' => 'Informazioni su questo file',
);

/** Japanese (日本語)
 * @author Aotake
 * @author Fryed-peach
 * @author JtFuruhata
 * @author Kahusi
 */
$messages['ja'] = array(
	'tmh-desc' => 'Theora および Vorbis 形式の Ogg ファイルハンドラーと JavaScript プレイヤー',
	'tmh-short-audio' => 'Ogg $1 音声ファイル、$2',
	'tmh-short-video' => 'Ogg $1 動画ファイル、$2',
	'tmh-short-general' => 'Ogg $1 メディアファイル、$2',
	'tmh-long-audio' => '(Ogg $1 音声ファイル、長さ $2、$3)',
	'tmh-long-video' => '(Ogg $1 動画ファイル、長さ $2、$4×$5px、$3)',
	'tmh-long-multiplexed' => '(Ogg 多重音声/動画ファイル、$1、長さ $2、$4×$5 ピクセル、$3)',
	'tmh-long-general' => '(Ogg メディアファイル、長さ $2、$3)',
	'tmh-long-error' => '(無効な Ogg ファイル: $1)',
	'tmh-play' => '再生',
	'tmh-pause' => '一時停止',
	'tmh-stop' => '停止',
	'tmh-play-video' => '動画を再生',
	'tmh-play-sound' => '音声を再生',
	'tmh-no-player' => '申し訳ありません、あなたのシステムには対応する再生ソフトウェアがインストールされていないようです。<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ここからダウンロードしてください</a>。',
	'tmh-no-xiphqt' => 'QuickTime 用 XiphQT コンポーネントがインストールされていないようです。QuickTime で Ogg ファイルを再生するには、このコンポーネントが必要です。<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ここから XiphQT をダウンロードする</a>か、別の再生ソフトをインストールしてください。',
	'tmh-player-videoElement' => 'ネイティヴ・ブラウザをサポート',
	'tmh-player-oggPlugin' => 'ブラウザ・プラグイン',
	'tmh-player-thumbnail' => '静止画像のみ',
	'tmh-player-soundthumb' => 'プレーヤー無し',
	'tmh-player-selected' => '(選択)',
	'tmh-use-player' => '利用するプレーヤー:',
	'tmh-more' => 'その他……',
	'tmh-dismiss' => '閉じる',
	'tmh-download' => 'ファイルをダウンロード',
	'tmh-desc-link' => 'ファイルの詳細',
	'tmh-oggThumb-version' => 'OggHandler は oggThumb バージョン$1またはそれ以降が必要です。',
	'tmh-oggThumb-failed' => 'oggThumb によるサムネイル作成に失敗しました。',
);

/** Jutish (Jysk)
 * @author Huslåke
 */
$messages['jut'] = array(
	'tmh-desc' => 'Håndlær før Ogg Theora og Vorbis filer, ve JavaScript spæler',
	'tmh-short-audio' => 'Ogg $1 sond file, $2',
	'tmh-short-video' => 'Ogg $1 video file, $2',
	'tmh-short-general' => 'Ogg $1 media file, $2',
	'tmh-long-audio' => '(Ogg $1 sond file, duråsje $2, $3)',
	'tmh-long-video' => '(Ogg $1 video file, duråsje $2, $4×$5 piksel, $3)',
	'tmh-long-multiplexed' => '(Ogg multipleksen audio/video file, $1, duråsje $2, $4×$5 piksler, $3 åverål)',
	'tmh-long-general' => '(Ogg $1 media file, duråsje $2, $3)',
	'tmh-long-error' => '(Ugyldegt ogg file: $2)',
	'tmh-play' => 'Spæl',
	'tmh-pause' => 'Pås',
	'tmh-stop' => 'Ståp',
	'tmh-play-video' => 'Spæl video',
	'tmh-play-sound' => 'Spæl sond',
	'tmh-no-player' => 'Unskyld, deres sistæm dä ekke appiære til har søm understønenge spæler softwær. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Nærlæĝ en spæler</a>.',
	'tmh-no-xiphqt' => 'Du däst ekke appiær til har æ XiphQT kompånent før QuickTime. QuickTime ken ekke spæl Ogg filer veud dette kompånent. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Nærlæĝ XiphQT</a> æller vælg\'en andes spæler.',
	'tmh-player-videoElement' => '<video> ælement',
	'tmh-player-oggPlugin' => 'Ogg plugin',
	'tmh-player-thumbnail' => 'Stil billet ålen',
	'tmh-player-soundthumb' => 'Ekke spæler',
	'tmh-player-selected' => '(sælektærn)',
	'tmh-use-player' => 'Brug spæler:',
	'tmh-more' => 'Mære...',
	'tmh-dismiss' => 'Slut',
	'tmh-download' => 'Nærlæĝ billet',
	'tmh-desc-link' => 'Åver dette file',
);

/** Javanese (Basa Jawa)
 * @author Meursault2004
 * @author Pras
 */
$messages['jv'] = array(
	'tmh-desc' => 'Sing ngurusi berkas Ogg Theora lan Vorbis mawa pamain JavaScript',
	'tmh-short-audio' => 'Berkas swara $1 ogg, $2',
	'tmh-short-video' => 'Berkas vidéo $1 ogg, $2',
	'tmh-short-general' => 'Berkas média $1 ogg, $2',
	'tmh-long-audio' => '(Berkas swara $1 ogg, dawané $2, $3)',
	'tmh-long-video' => '(Berkas vidéo $1 ogg, dawané $2, $4×$5 piksel, $3)',
	'tmh-long-multiplexed' => '(Berkas audio/vidéo multiplexed ogg, $1, dawané $2, $4×$5 piksel, $3 gunggungé)',
	'tmh-long-general' => '(Berkas média ogg, dawané $2, $3)',
	'tmh-long-error' => '(Berkas ogg ora absah: $1)',
	'tmh-play' => 'Main',
	'tmh-pause' => 'Lèrèn',
	'tmh-stop' => 'Mandeg',
	'tmh-play-video' => 'Main vidéo',
	'tmh-play-sound' => 'Main swara',
	'tmh-no-player' => 'Nuwun sèwu, sistém panjenengan katoné ora ndarbèni siji-sijia piranti empuk sing didukung.
Mangga <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ngundhuh salah siji piranti pamain</a>.',
	'tmh-no-xiphqt' => 'Katoné panjenengan ora ana komponèn XiphQT kanggo QuickTime.
QuickTime ora bisa mainaké berkas-berkas Ogg tanpa komponèn iki.
Please <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ngundhuh XiphQT</a> utawa milih piranti pamain liya.',
	'tmh-player-videoElement' => 'Dhukungan browser asli',
	'tmh-player-oggPlugin' => "''Plugin browser''",
	'tmh-player-thumbnail' => 'Namung gambar statis waé',
	'tmh-player-soundthumb' => 'Ora ana piranti pamain',
	'tmh-player-selected' => '(dipilih)',
	'tmh-use-player' => 'Nganggo piranti pamain:',
	'tmh-more' => 'Luwih akèh...',
	'tmh-dismiss' => 'Tutup',
	'tmh-download' => 'Undhuh berkas',
	'tmh-desc-link' => 'Prekara berkas iki',
);

/** Georgian (ქართული)
 * @author BRUTE
 * @author Malafaya
 * @author გიორგიმელა
 */
$messages['ka'] = array(
	'tmh-short-video' => 'Ogg $1 ვიდეო ფაილი, $2',
	'tmh-short-general' => 'Ogg $1 მედია ფაილი, $2',
	'tmh-play' => 'თამაში',
	'tmh-pause' => 'პაუზა',
	'tmh-stop' => 'შეჩერება',
	'tmh-play-video' => 'ვიდეოს ჩართვა',
	'tmh-play-sound' => 'ხმის ტამაში',
	'tmh-player-soundthumb' => 'No player',
	'tmh-player-selected' => '(არჩეულია)',
	'tmh-more' => 'მეტი...',
	'tmh-dismiss' => 'დახურვა',
	'tmh-download' => 'ფაილის ჩამოტვირთვა',
	'tmh-desc-link' => 'ამ ფაილის შესახებ',
);

/** Kazakh (Arabic script) (‫قازاقشا (تٴوتە)‬) */
$messages['kk-arab'] = array(
	'tmh-short-audio' => 'Ogg $1 دىبىس فايلى, $2',
	'tmh-short-video' => 'Ogg $1 بەينە فايلى, $2',
	'tmh-short-general' => 'Ogg $1 تاسپا فايلى, $2',
	'tmh-long-audio' => '(Ogg $1 دىبىس فايلى, ۇزاقتىعى $2, $3)',
	'tmh-long-video' => '(Ogg $1 بەينە فايلى, ۇزاقتىعى $2, $4 × $5 پىيكسەل, $3)',
	'tmh-long-multiplexed' => '(Ogg قۇرامدى دىبىس/بەينە فايلى, $1, ۇزاقتىعى $2, $4 × $5 پىيكسەل, $3 نە بارلىعى)',
	'tmh-long-general' => '(Ogg تاسپا فايلى, ۇزاقتىعى $2, $3)',
	'tmh-long-error' => '(جارامسىز ogg فايلى: $1)',
	'tmh-play' => 'ويناتۋ',
	'tmh-pause' => 'ايالداتۋ',
	'tmh-stop' => 'توقتاتۋ',
	'tmh-play-video' => 'بەينەنى ويناتۋ',
	'tmh-play-sound' => 'دىبىستى ويناتۋ',
	'tmh-no-player' => 'عافۋ ەتىڭىز, جۇيەڭىزدە ەش سۇيەمەلدەگەن ويناتۋ باعدارلامالىق قامتاماسىزداندىرعىش ورناتىلماعان. <a href="http://www.java.com/en/download/manual.jsp">Java</a> بۋماسىن ورناتىپ شىعىڭىز.',
	'tmh-no-xiphqt' => 'QuickTime ويناتقىشىڭىزدىڭ XiphQT دەگەن قۇراشى جوق سىيياقتى. بۇل قۇراشىسىز Ogg فايلدارىن QuickTime ويناتا المايدى. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT قۇراشىن</a> نە باسقا ويناتقىشتى جۇكتەڭىز.',
	'tmh-player-videoElement' => '<video> داناسى',
	'tmh-player-oggPlugin' => 'Ogg قوسىمشا باعدارلاماسى',
	'tmh-player-thumbnail' => 'تەك ستوپ-كادر',
	'tmh-player-soundthumb' => 'ويناتقىشسىز',
	'tmh-player-selected' => '(بولەكتەلگەن)',
	'tmh-use-player' => 'ويناتقىش پايدالانۋى:',
	'tmh-more' => 'كوبىرەك...',
	'tmh-dismiss' => 'جابۋ',
	'tmh-download' => 'فايلدى جۇكتەۋ',
	'tmh-desc-link' => 'بۇل فايل تۋرالى',
);

/** Kazakh (Cyrillic) (Қазақша (Cyrillic)) */
$messages['kk-cyrl'] = array(
	'tmh-short-audio' => 'Ogg $1 дыбыс файлы, $2',
	'tmh-short-video' => 'Ogg $1 бейне файлы, $2',
	'tmh-short-general' => 'Ogg $1 таспа файлы, $2',
	'tmh-long-audio' => '(Ogg $1 дыбыс файлы, ұзақтығы $2, $3)',
	'tmh-long-video' => '(Ogg $1 бейне файлы, ұзақтығы $2, $4 × $5 пиксел, $3)',
	'tmh-long-multiplexed' => '(Ogg құрамды дыбыс/бейне файлы, $1, ұзақтығы $2, $4 × $5 пиксел, $3 не барлығы)',
	'tmh-long-general' => '(Ogg таспа файлы, ұзақтығы $2, $3)',
	'tmh-long-error' => '(Жарамсыз ogg файлы: $1)',
	'tmh-play' => 'Ойнату',
	'tmh-pause' => 'Аялдату',
	'tmh-stop' => 'Тоқтату',
	'tmh-play-video' => 'Бейнені ойнату',
	'tmh-play-sound' => 'Дыбысты ойнату',
	'tmh-no-player' => 'Ғафу етіңіз, жүйеңізде еш сүйемелдеген ойнату бағдарламалық қамтамасыздандырғыш орнатылмаған. <a href="http://www.java.com/en/download/manual.jsp">Java</a> бумасын орнатып шығыңыз.',
	'tmh-no-xiphqt' => 'QuickTime ойнатқышыңыздың XiphQT деген құрашы жоқ сияқты. Бұл құрашысыз Ogg файлдарын QuickTime ойната алмайды. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT құрашын</a> не басқа ойнатқышты жүктеңіз.',
	'tmh-player-videoElement' => '<video> данасы',
	'tmh-player-oggPlugin' => 'Ogg қосымша бағдарламасы',
	'tmh-player-thumbnail' => 'Тек стоп-кадр',
	'tmh-player-soundthumb' => 'Ойнатқышсыз',
	'tmh-player-selected' => '(бөлектелген)',
	'tmh-use-player' => 'Ойнатқыш пайдалануы:',
	'tmh-more' => 'Көбірек...',
	'tmh-dismiss' => 'Жабу',
	'tmh-download' => 'Файлды жүктеу',
	'tmh-desc-link' => 'Бұл файл туралы',
);

/** Kazakh (Latin) (Қазақша (Latin)) */
$messages['kk-latn'] = array(
	'tmh-short-audio' => 'Ogg $1 dıbıs faýlı, $2',
	'tmh-short-video' => 'Ogg $1 beýne faýlı, $2',
	'tmh-short-general' => 'Ogg $1 taspa faýlı, $2',
	'tmh-long-audio' => '(Ogg $1 dıbıs faýlı, uzaqtığı $2, $3)',
	'tmh-long-video' => '(Ogg $1 beýne faýlı, uzaqtığı $2, $4 × $5 pïksel, $3)',
	'tmh-long-multiplexed' => '(Ogg quramdı dıbıs/beýne faýlı, $1, uzaqtığı $2, $4 × $5 pïksel, $3 ne barlığı)',
	'tmh-long-general' => '(Ogg taspa faýlı, uzaqtığı $2, $3)',
	'tmh-long-error' => '(Jaramsız ogg faýlı: $1)',
	'tmh-play' => 'Oýnatw',
	'tmh-pause' => 'Ayaldatw',
	'tmh-stop' => 'Toqtatw',
	'tmh-play-video' => 'Beýneni oýnatw',
	'tmh-play-sound' => 'Dıbıstı oýnatw',
	'tmh-no-player' => 'Ğafw etiñiz, jüýeñizde eş süýemeldegen oýnatw bağdarlamalıq qamtamasızdandırğış ornatılmağan. <a href="http://www.java.com/en/download/manual.jsp">Java</a> bwmasın ornatıp şığıñız.',
	'tmh-no-xiphqt' => 'QuickTime oýnatqışıñızdıñ XiphQT degen quraşı joq sïyaqtı. Bul quraşısız Ogg faýldarın QuickTime oýnata almaýdı. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT quraşın</a> ne basqa oýnatqıştı jükteñiz.',
	'tmh-player-videoElement' => '<video> danası',
	'tmh-player-oggPlugin' => 'Ogg qosımşa bağdarlaması',
	'tmh-player-thumbnail' => 'Tek stop-kadr',
	'tmh-player-soundthumb' => 'Oýnatqışsız',
	'tmh-player-selected' => '(bölektelgen)',
	'tmh-use-player' => 'Oýnatqış paýdalanwı:',
	'tmh-more' => 'Köbirek...',
	'tmh-dismiss' => 'Jabw',
	'tmh-download' => 'Faýldı jüktew',
	'tmh-desc-link' => 'Bul faýl twralı',
);

/** Khmer (ភាសាខ្មែរ)
 * @author Chhorran
 * @author Lovekhmer
 * @author T-Rithy
 * @author Thearith
 * @author គីមស៊្រុន
 */
$messages['km'] = array(
	'tmh-desc' => 'គាំទ្រចំពោះ Ogg Theora និង Vorbis files, ជាមួយ ឧបករណ៍អាន JavaScript',
	'tmh-short-audio' => 'ឯកសារ សំឡេង Ogg $1, $2',
	'tmh-short-video' => 'ឯកសារវីដេអូ Ogg $1, $2',
	'tmh-short-general' => 'ឯកសារមេឌាOgg $1, $2',
	'tmh-long-audio' => '(ឯកសារសំឡេងប្រភេទOgg $1, រយៈពេល$2 និងទំហំ$3)',
	'tmh-long-video' => '(ឯកសារវីដេអូប្រភេទOgg $1, រយៈពេល$2, $4×$5px, $3)',
	'tmh-long-multiplexed' => '(ឯកសារអូឌីយ៉ូ/វីដេអូចម្រុះប្រភេទOgg , $1, រយៈពេល$2, $4×$5px, ប្រហែល$3)',
	'tmh-long-general' => '(ឯកសារមេឌាប្រភេទOgg, រយៈពេល$2, $3)',
	'tmh-long-error' => '(ឯកសារ ogg មិនមាន សុពលភាព ៖ $1)',
	'tmh-play' => 'លេង',
	'tmh-pause' => 'ផ្អាក',
	'tmh-stop' => 'ឈប់',
	'tmh-play-video' => 'លេងវីដេអូ',
	'tmh-play-sound' => 'បន្លឺសំឡេង',
	'tmh-no-player' => 'សូមអភ័យទោស! ប្រព័ន្ធដំណើរការរបស់អ្នក ហាក់បីដូចជាមិនមានកម្មវិធី ណាមួយសម្រាប់លេងទេ។ សូម <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ទាញយកកម្មវិធី សម្រាប់លេងនៅទីនេះ</a> ។',
	'tmh-no-xiphqt' => 'មិនឃើញមាន អង្គផ្សំ XiphQT សម្រាប់ QuickTime។ QuickTime មិនអាចអាន ឯកសារ ដោយ គ្មាន អង្គផ្សំនេះ។ ទាញយក <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download"> និង ដំឡើង XiphQT</a> ឬ ជ្រើសរើស ឧបករណ៍អាន ផ្សេង ។',
	'tmh-player-videoElement' => 'Native browser support',
	'tmh-player-oggPlugin' => 'កម្មវិធីជំនួយ​របស់​កម្មវិធីរុករក',
	'tmh-player-thumbnail' => 'នៅតែជារូបភាព',
	'tmh-player-soundthumb' => 'មិនមានឧបករណ៍លេងទេ',
	'tmh-player-selected' => '(បានជ្រើសយក)',
	'tmh-use-player' => 'ប្រើប្រាស់ឧបករណ៍លេង៖',
	'tmh-more' => 'បន្ថែម...',
	'tmh-dismiss' => 'បិទ',
	'tmh-download' => 'ទាញយកឯកសារ',
	'tmh-desc-link' => 'អំពីឯកសារនេះ',
);

/** Korean (한국어)
 * @author ITurtle
 * @author Kwj2772
 * @author ToePeu
 */
$messages['ko'] = array(
	'tmh-desc' => 'OGG Theora 및 Vorbis 파일 핸들러와 자바스크립트 플레이어',
	'tmh-short-audio' => 'Ogg $1 소리 파일, $2',
	'tmh-short-video' => 'Ogg $1 영상 파일, $2',
	'tmh-short-general' => 'Ogg $1 미디어 파일, $2',
	'tmh-long-audio' => '(Ogg $1 소리 파일, 길이 $2, $3)',
	'tmh-long-video' => '(Ogg $1 영상 파일, 길이 $2, $4×$5 픽셀, $3)',
	'tmh-long-multiplexed' => '(Ogg 다중 소리/영상 파일, $1, 길이 $2, $4×$5 픽셀, 대략 $3)',
	'tmh-long-general' => '(Ogg 미디어 파일, 길이 $2, $3)',
	'tmh-long-error' => '(잘못된 ogg 파일: $1)',
	'tmh-play' => '재생',
	'tmh-pause' => '일시정지',
	'tmh-stop' => '정지',
	'tmh-play-video' => '영상 재생하기',
	'tmh-play-sound' => '소리 재생하기',
	'tmh-no-player' => '죄송합니다. 이 시스템에는 재생을 지원하는 플레이어가 설치되지 않은 것 같습니다. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">플레이어를 내려받으세요.</a>',
	'tmh-no-xiphqt' => 'QuickTime의 XiphQT 구성 요소가 없는 것 같습니다.
QuickTime은 이 구성 요소 없이는 Ogg 파일을 재생할 수 없습니다.
<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download"> XiphQT를 내려받거나</a> 다른 플레이어를 선택하십시오.',
	'tmh-player-videoElement' => '기본 브라우저 지원',
	'tmh-player-oggPlugin' => '브라우저 플러그인',
	'tmh-player-thumbnail' => '정지 화면만',
	'tmh-player-soundthumb' => '플레이어 없음',
	'tmh-player-selected' => '(선택함)',
	'tmh-use-player' => '사용할 플레이어:',
	'tmh-more' => '더 보기...',
	'tmh-dismiss' => '닫기',
	'tmh-download' => '파일 다운로드',
	'tmh-desc-link' => '파일 정보',
	'tmh-oggThumb-version' => 'OggHandler는 oggThumb 버전 $1 이상을 요구합니다.',
	'tmh-oggThumb-failed' => 'oggThumb가 섬네일을 생성하지 못했습니다.',
);

/** Kinaray-a (Kinaray-a)
 * @author Jose77
 */
$messages['krj'] = array(
	'tmh-more' => 'Raku pa...',
);

/** Colognian (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'tmh-desc' => 'En Projamm (<i lang="en">handler</i>) för <i lang="en">Ogg Theora</i> un <i lang="en">Ogg Vorbis</i> Dateie, met enem Javaskrip Afspiller.',
	'tmh-short-audio' => '<i lang="en">Ogg $1</i> Tondatei, $2',
	'tmh-short-video' => '<i lang="en">Ogg $1</i> Viddejodatei, $2',
	'tmh-short-general' => '<i lang="en">Ogg $1</i> Medijedatei, $2',
	'tmh-long-audio' => '(<i lang="en">Ogg $1</i> Tondatei fum Ömfang $2, $3)',
	'tmh-long-video' => '(<i lang="en">Ogg $1</i> Viddejodatei fum Ömfang $2 un {{PLURAL:$4|ein Pixel|$4 Pixelle|kei Pixel}} × {{PLURAL:$5|ei Pixel|$4 Pixelle|kei Pixel}}, $3)',
	'tmh-long-multiplexed' => '(<i lang="en">Ogg</i> jemultipex Ton- un Viddejodatei, $1, fum Ömfang $2 un {{PLURAL:$4|ein Pixel|$4 Pixelle|kei Pixel}} × {{PLURAL:$5|ei Pixel|$4 Pixelle|kei Pixel}}, $3 ennsjesammp)',
	'tmh-long-general' => '(<i lang="en">Ogg</i> Medijedatei fum Ömfang $2, $3)',
	'tmh-long-error' => '(ene kapodde <i lang="en">Ogg</i> Datei: $1)',
	'tmh-play' => 'Loßläje!',
	'tmh-pause' => 'Aanhallde!',
	'tmh-stop' => 'Ophüre!',
	'tmh-play-video' => 'Dun der Viddejo affshpelle',
	'tmh-play-sound' => 'Dä Ton afshpelle',
	'tmh-no-player' => 'Deijt mer leid, süüd_esu uß, wi wann Dinge Kompjutor kei
Affspellprojramm hät, wat mer öngerstoze däte.
Beß esu joot, un <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">donn e Affspellprojramm erunger lade</a>.',
	'tmh-no-xiphqt' => 'Deijt mer leid, süüd_esu uß, wi wann Dinge Kompjutor nit
dat XiphQT Affspellprojrammstöck för <i lang="en">QuickTime</i> hät,
ävver <i lang="en">QuickTime</i> kann <i lang="en">Ogg</i>-Dateie
der oohne nit affspelle.
Beß esu joot, un <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">donn dat XiphQT erunger lade</a>,
udder sök Der en annder Affspellprojramm uß.',
	'tmh-player-videoElement' => 'Ongerstözung för Brauser',
	'tmh-player-oggPlugin' => 'Brauser <i lang="en">Plug-In</i>',
	'tmh-player-cortado' => 'Cortado (Java)',
	'tmh-player-vlc-mozilla' => 'VLC',
	'tmh-player-vlc-activex' => 'VLC (<i lang="en">ActiveX</i>)',
	'tmh-player-quicktime-mozilla' => '<i lang="en">QuickTime</i>',
	'tmh-player-quicktime-activex' => '<i lang="en">QuickTime</i> (<i lang="en">ActiveX</i>)',
	'tmh-player-totem' => 'Totem',
	'tmh-player-kmplayer' => 'KM<i lang="en">Player</i>',
	'tmh-player-kaffeine' => '<i lang="en">Kaffeine</i>',
	'tmh-player-mplayerplug-in' => '<i lang="en">mplayerplug-in</i>',
	'tmh-player-thumbnail' => 'Bloß e Standbeld',
	'tmh-player-soundthumb' => 'Kei Affspellprojramm',
	'tmh-player-selected' => '(Ußjesoht)',
	'tmh-use-player' => 'Affspellprojramm:',
	'tmh-more' => 'Enshtelle&nbsp;…',
	'tmh-dismiss' => 'Zomaache!',
	'tmh-download' => 'Datei erunger lade',
	'tmh-desc-link' => 'Övver di Datei',
);

/** Latin (Latina)
 * @author SPQRobin
 */
$messages['la'] = array(
	'tmh-more' => 'Plus...',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Les Meloures
 * @author Robby
 */
$messages['lb'] = array(
	'tmh-desc' => 'Steierungsprogramm fir Ogg Theora a Vorbis Fichieren, mat enger JavaScript-Player-Software',
	'tmh-short-audio' => 'tmh-$1-Tounfichier, $2',
	'tmh-short-video' => 'tmh-$1-Videofichier, $2',
	'tmh-short-general' => 'tmh-$1-Mediefichier, $2',
	'tmh-long-audio' => '(tmh-$1-Tounfichier, Dauer: $2, $3)',
	'tmh-long-video' => '(tmh-$1-Videofichier, Dauer: $2, $4×$5 Pixel, $3)',
	'tmh-long-multiplexed' => '(tmh-Toun-/Video-Fichier, $1, Dauer: $2, $4×$5 Pixel, $3)',
	'tmh-long-general' => '(Ogg Media-Fichier, Dauer $2, $3)',
	'tmh-long-error' => '(Ongëltegen tmh-Fichier: $1)',
	'tmh-play' => 'Ofspillen',
	'tmh-pause' => 'Paus',
	'tmh-stop' => 'Stopp',
	'tmh-play-video' => 'Video ofspillen',
	'tmh-play-sound' => 'Tounfichier ofspillen',
	'tmh-no-player' => 'Pardon, Äre Betriibssystem schengt keng Software ze hunn fir d\'Fichieren ofzespillen. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Lued w.e.g. esou eng Software erof</a> an installéiert se w.e.g. .',
	'tmh-no-xiphqt' => 'Dir hutt anscheinend d\'Komponent  XiphQT fir QuickTime net installéiert.
QuickTime kann tmh-Fichiere net ouni dës Komponent spillen.
<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Lued XiphQT w.e.g. erof</a> oder wielt eng aner Software.',
	'tmh-player-videoElement' => 'Ënnerstëtzt duerch de Browser',
	'tmh-player-oggPlugin' => 'Browser-Plugin',
	'tmh-player-thumbnail' => 'Just als Bild weisen',
	'tmh-player-soundthumb' => 'Keng Player-Software',
	'tmh-player-selected' => '(erausgewielt)',
	'tmh-use-player' => "Benotzt d'Player-Software:",
	'tmh-more' => 'Méi ...',
	'tmh-dismiss' => 'Zoumaachen',
	'tmh-download' => 'Fichier eroflueden',
	'tmh-desc-link' => 'Iwwer dëse Fichier',
);

/** Lingua Franca Nova (Lingua Franca Nova)
 * @author Malafaya
 */
$messages['lfn'] = array(
	'tmh-more' => 'Plu…',
);

/** Limburgish (Limburgs)
 * @author Matthias
 * @author Ooswesthoesbes
 */
$messages['li'] = array(
	'tmh-desc' => "Handelt Ogg Theora- en Vorbis-bestande aaf met 'n JavaScript-mediaspeler",
	'tmh-short-audio' => 'Ogg $1 geluidsbestandj, $2',
	'tmh-short-video' => 'Ogg $1 videobestandj, $2',
	'tmh-short-general' => 'Ogg $1 mediabestandj, $2',
	'tmh-long-audio' => '(Ogg $1 geluidsbestandj, lingdje $2, $3)',
	'tmh-long-video' => '(Ogg $1 videobestandj, lingdje $2, $4×$5 pixels, $3)',
	'tmh-long-multiplexed' => '(Ogg gemultiplexeerd geluids-/videobestandj, $1, lingdje $2, $4×$5 pixels, $3 totaal)',
	'tmh-long-general' => '(Ogg mediabestandj, lingdje $2, $3)',
	'tmh-long-error' => '(Óngeljig oggg-bestandj: $1)',
	'tmh-play' => 'Aafspele',
	'tmh-pause' => 'Óngerbraeke',
	'tmh-stop' => 'Oetsjeije',
	'tmh-play-video' => 'Video aafspele',
	'tmh-play-sound' => 'Geluid aafspele',
	'tmh-no-player' => 'Sorry, uch systeem haet gein van de ongersteunde mediaspelers. Installeer estebleef <a href="http://www.java.com/nl/download/manual.jsp">Java</a>.',
	'tmh-no-xiphqt' => "'t Liek d'r op det geer 't component XiphQT veur QuickTime neet haet. QuickTime kin tmh-bestenj neet aafspele zonger dit component. Download <a href=\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\">XiphQT</a> estebleef of kees 'ne angere speler.",
	'tmh-player-videoElement' => 'Native browsersupport',
	'tmh-player-oggPlugin' => 'Browserplugin',
	'tmh-player-thumbnail' => 'Allein stilstaondj beild',
	'tmh-player-soundthumb' => 'Geine mediaspeler',
	'tmh-player-selected' => '(geselectieërdj)',
	'tmh-use-player' => 'Gebroek speler:',
	'tmh-more' => 'Mieë...',
	'tmh-dismiss' => 'Sloet',
	'tmh-download' => 'Bestandj downloade',
	'tmh-desc-link' => 'Euver dit bestandj',
);

/** Lithuanian (Lietuvių)
 * @author Homo
 * @author Matasg
 */
$messages['lt'] = array(
	'tmh-desc' => 'Įrankis groti Ogg Theora ir Vorbis failus su JavaScript grotuvu',
	'tmh-short-audio' => 'Ogg $1 garso byla, $2',
	'tmh-short-video' => 'Ogg $1 video byla, $2',
	'tmh-short-general' => 'Ogg $1 medija byla, $2',
	'tmh-long-audio' => '(Ogg $1 garso byla, ilgis $2, $3)',
	'tmh-long-video' => '(Ogg $1 video byla, ilgis $2, $4×$5 pikseliai, $3)',
	'tmh-long-multiplexed' => '(Ogg sutankinta audio/video byla, $1, ilgis $2, $4×$5 pikseliai, $3 viso)',
	'tmh-long-general' => '(Ogg media byla, ilgis $2, $3)',
	'tmh-long-error' => '(Bloga ogg byla: $1)',
	'tmh-play' => 'Groti',
	'tmh-pause' => 'Pauzė',
	'tmh-stop' => 'Sustabdyti',
	'tmh-play-video' => 'Groti video',
	'tmh-play-sound' => 'Groti garsą',
	'tmh-no-player' => 'Atsiprašome, neatrodo, kad jūsų sistema turi palaikomą grotuvą. Prašome <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">jį atsisiųsti</a>.',
	'tmh-no-xiphqt' => 'Neatrodo, kad jūs turite XiphQT komponentą QuickTime grotuvui. QuickTime negali groti Ogg bylų be šio komponento. Prašome <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">atsisiųsti XiphQT</a> arba pasirinkti kitą grotuvą.',
	'tmh-player-videoElement' => 'Pagrindinės naršyklės palaikymas',
	'tmh-player-oggPlugin' => 'Naršyklės priedas',
	'tmh-player-thumbnail' => 'Tik paveikslėlis',
	'tmh-player-soundthumb' => 'Nėra grotuvo',
	'tmh-player-selected' => '(pasirinkta)',
	'tmh-use-player' => 'Naudoti grotuvą:',
	'tmh-more' => 'Daugiau...',
	'tmh-dismiss' => 'Uždaryti',
	'tmh-download' => 'Atsisiųsti bylą',
	'tmh-desc-link' => 'Apie šią bylą',
);

/** Latvian (Latviešu)
 * @author Xil
 */
$messages['lv'] = array(
	'tmh-dismiss' => 'Aizvērt',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 * @author Brest
 */
$messages['mk'] = array(
	'tmh-desc' => 'Ракувач со Ogg Theora и Vorbis податотеки, со помош на JavaScript преслушувач/прегледувач',
	'tmh-short-audio' => 'Ogg $1 звучна податотека, $2',
	'tmh-short-video' => 'Ogg $1 видео податотека, $2',
	'tmh-short-general' => 'Ogg $1 медија податотека, $2',
	'tmh-long-audio' => '(Ogg $1 звучна податотека, должина $2, $3)',
	'tmh-long-video' => '(Ogg $1 видео податотека, должина $2, $4×$5 пиксели, $3)',
	'tmh-long-multiplexed' => '(Ogg мултиплексирана аудио/видео податотека, $1, должина $2, $4×$5 пиксели, $3 вкупно)',
	'tmh-long-general' => '(Ogg медија податотека, должина $2, $3)',
	'tmh-long-error' => '(Оштетена ogg податотека: $1)',
	'tmh-play' => 'Почни',
	'tmh-pause' => 'Паузирај',
	'tmh-stop' => 'Стопирај',
	'tmh-play-video' => 'Пушти видеоснимка',
	'tmh-play-sound' => 'Слушни аудио снимка',
	'tmh-no-player' => 'Изгледа дека вашиот систем нема инсталирано било каков софтвер за преслушување/прегледување на аудио или видео записи.
Можете <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">да симнете некој софтвер за оваа намена од тука</a>.',
	'tmh-no-xiphqt' => 'Изгледа ја немате инсталирано XiphQT компонентата за QuickTime.
QuickTime не може да преслушува/прегледува Ogg податотеки без оваа компонента.
Можете да го <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">симнете XiphQT</a> или да изберете некој друг софтвер за преслушување/прегледување.',
	'tmh-player-videoElement' => 'Подржано од прелистувачот',
	'tmh-player-oggPlugin' => 'Вградено во прелистувачот',
	'tmh-player-cortado' => 'Cortado (Java)',
	'tmh-player-vlc-mozilla' => 'VLC',
	'tmh-player-vlc-activex' => 'VLC (ActiveX)',
	'tmh-player-quicktime-mozilla' => 'QuickTime',
	'tmh-player-quicktime-activex' => 'QuickTime (ActiveX)',
	'tmh-player-totem' => 'Totem',
	'tmh-player-kmplayer' => 'KMPlayer',
	'tmh-player-kaffeine' => 'Kaffeine',
	'tmh-player-mplayerplug-in' => 'mplayerplug-in',
	'tmh-player-thumbnail' => 'Само неподвижни слики',
	'tmh-player-soundthumb' => 'Нема инсталирано преслушувач',
	'tmh-player-selected' => '(избрано)',
	'tmh-use-player' => 'Користи:',
	'tmh-more' => 'Повеќе...',
	'tmh-dismiss' => 'Затвори',
	'tmh-download' => 'Симни податотека',
	'tmh-desc-link' => 'Информации за оваа податотека',
	'tmh-oggThumb-version' => 'OggHandler бара oggThumb верзија $1 или понова.',
	'tmh-oggThumb-failed' => 'oggThumb не успеа да ја создаде минијатурата.',
);

/** Malayalam (മലയാളം)
 * @author Praveenp
 * @author Shijualex
 */
$messages['ml'] = array(
	'tmh-desc' => 'ജാവാസ്ക്രിപ്റ്റ് പ്ലേയർ ഉപയോഗിച്ച് ഓഗ് തിയോറ, വോർബിസ് പ്രമാണങ്ങൾ കൈകാര്യം ചെയ്യൽ',
	'tmh-short-audio' => 'ഓഗ് $1 ശബ്ദപ്രമാണം, $2',
	'tmh-short-video' => 'ഓഗ് $1 വീഡിയോ പ്രമാണം, $2',
	'tmh-short-general' => 'ഓഗ് $1 മീഡിയ പ്രമാണം, $2',
	'tmh-long-audio' => '(ഓഗ് $1 ശബ്ദ പ്രമാണം, ദൈർഘ്യം $2, $3)',
	'tmh-long-video' => '(ഓഗ് $1 വീഡിയോ പ്രമാണം, ദൈർഘ്യം $2, $4×$5 pixels, $3)',
	'tmh-long-multiplexed' => '(ഓഗ് മൾട്ടിപ്ലക്സ്‌‌ഡ് ശബ്ദ/ചലച്ചിത്ര പ്രമാണം, $1, ദൈർഘ്യം $2, $4×$5 ബിന്ദു, ആകെക്കൂടി $3)',
	'tmh-long-general' => '(ഓഗ് മീഡിയ പ്രമാണം, ദൈർഘ്യം $2, $3)',
	'tmh-long-error' => '(അസാധുവായ ഓഗ് പ്രമാണം: $1)',
	'tmh-play' => 'പ്രവർത്തിപ്പിക്കുക',
	'tmh-pause' => 'താൽക്കാലികമായി നിർത്തുക',
	'tmh-stop' => 'നിർത്തുക',
	'tmh-play-video' => 'വീഡിയോ പ്രവർത്തിപ്പിക്കുക',
	'tmh-play-sound' => 'ശബ്ദം പ്രവർത്തിപ്പിക്കുക',
	'tmh-no-player' => 'ക്ഷമിക്കണം. താങ്കളുടെ കമ്പ്യൂട്ടറിൽ ഓഗ് പ്രമാണം പ്രവർത്തിപ്പിക്കാനാവശ്യമായ സോഫ്റ്റ്‌ഫെയർ ഇല്ല. ദയവു ചെയ്ത് ഒരു പ്ലെയർ <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ഡൗൺലോഡ് ചെയ്യുക</a>.',
	'tmh-no-xiphqt' => 'ക്വിക്ക്റ്റൈമിനുള്ള XiphQT ഘടകം താങ്കളുടെ പക്കലുണ്ടെന്നു കാണുന്നില്ല.
ഓഗ് പ്രമാണങ്ങൾ ഈ ഘടകമില്ലാതെ പ്രവർത്തിപ്പിക്കാൻ ക്വിക്ക്റ്റൈമിനു കഴിയില്ല.
ദയവായി <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT ഡൗൺലോഡ് ചെയ്യുക</a> അല്ലെങ്കിൽ മറ്റൊരു പ്ലേയർ തിരഞ്ഞെടുക്കുക.',
	'tmh-player-videoElement' => 'ബ്രൗസറിൽ സ്വതവേയുള്ള പിന്തുണ',
	'tmh-player-oggPlugin' => 'ബ്രൗസർ പ്ലഗിൻ',
	'tmh-player-quicktime-mozilla' => 'ക്വിക്ക്റ്റൈം',
	'tmh-player-quicktime-activex' => 'ക്വിക്ക്റ്റൈം (ആക്റ്റീവ്‌‌എക്സ്)',
	'tmh-player-thumbnail' => 'നിശ്ചല ചിത്രം മാത്രം',
	'tmh-player-soundthumb' => 'പ്ലെയർ ഇല്ല',
	'tmh-player-selected' => '(തിരഞ്ഞെടുത്തവ)',
	'tmh-use-player' => 'ഈ പ്ലെയർ ഉപയോഗിക്കുക',
	'tmh-more' => 'കൂടുതൽ...',
	'tmh-dismiss' => 'അടയ്ക്കുക',
	'tmh-download' => 'പ്രമാണം ഡൗൺലോഡ് ചെയ്യുക',
	'tmh-desc-link' => 'ഈ പ്രമാണത്തെക്കുറിച്ച്',
	'tmh-oggThumb-version' => 'ഓഗ്-തമ്പ് പതിപ്പ് $1 അല്ലെങ്കിൽ പുതിയത് ഓഗ്-ഹാൻഡ്ലറിനാവശ്യമാണ്.',
	'tmh-oggThumb-failed' => 'ലഘുചിത്രം സൃഷ്ടിക്കുന്നതിൽ ഓഗ്-തമ്പ് പരാജയപ്പെട്ടു.',
);

/** Marathi (मराठी)
 * @author Kaustubh
 */
$messages['mr'] = array(
	'tmh-desc' => 'ऑग थियोरा व वॉर्बिस संचिकांसाठीचा चालक, जावास्क्रीप्ट प्लेयर सकट',
	'tmh-short-audio' => 'ऑग $1 ध्वनी संचिका, $2',
	'tmh-short-video' => 'ऑग $1 चलतचित्र संचिका, $2',
	'tmh-short-general' => 'ऑग $1 मीडिया संचिका, $2',
	'tmh-long-audio' => '(ऑग $1 ध्वनी संचिका, लांबी $2, $3)',
	'tmh-long-video' => '(ऑग $1 चलतचित्र संचिका, लांबी $2, $4×$5 पीक्सेल्स, $3)',
	'tmh-long-multiplexed' => '(ऑग ध्वनी/चित्र संचिका, $1, लांबी $2, $4×$5 पिक्सेल्स, $3 एकूण)',
	'tmh-long-general' => '(ऑग मीडिया संचिका, लांबी $2, $3)',
	'tmh-long-error' => '(चुकीची ऑग संचिका: $1)',
	'tmh-play' => 'चालू करा',
	'tmh-pause' => 'विराम',
	'tmh-stop' => 'थांबवा',
	'tmh-play-video' => 'चलतचित्र चालू करा',
	'tmh-play-sound' => 'ध्वनी चालू करा',
	'tmh-no-player' => 'माफ करा, पण तुमच्या संगणकामध्ये कुठलाही प्लेयर आढळला नाही. कृपया <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">प्लेयर डाउनलोड करा</a>.',
	'tmh-no-xiphqt' => 'तुमच्या संगणकामध्ये क्वीकटाईम ला लागणारा XiphQT हा तुकडा आढळला नाही. याशिवाय क्वीकटाईम ऑग संचिका चालवू शकणार नाही. कॄपया <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT डाउनलोड करा</a> किंवा दुसरा प्लेयर वापरा.',
	'tmh-player-videoElement' => '<video> तुकडा',
	'tmh-player-oggPlugin' => 'ऑग प्लगीन',
	'tmh-player-cortado' => 'कोर्टाडो (जावा)',
	'tmh-player-thumbnail' => 'फक्त स्थिर चित्र',
	'tmh-player-soundthumb' => 'प्लेयर उपलब्ध नाही',
	'tmh-player-selected' => '(निवडलेले)',
	'tmh-use-player' => 'हा प्लेयर वापरा:',
	'tmh-more' => 'आणखी...',
	'tmh-dismiss' => 'बंद करा',
	'tmh-download' => 'संचिका उतरवा',
	'tmh-desc-link' => 'या संचिकेबद्दलची माहिती',
);

/** Malay (Bahasa Melayu)
 * @author Aviator
 */
$messages['ms'] = array(
	'tmh-desc' => 'Pengelola fail Ogg Theora dan Vorbis, dengan pemain JavaScript',
	'tmh-short-audio' => 'fail bunyi Ogg $1, $2',
	'tmh-short-video' => 'fail video Ogg $1, $2',
	'tmh-short-general' => 'fail media Ogg $1, $2',
	'tmh-long-audio' => '(fail bunyi Ogg $1, tempoh $2, $3)',
	'tmh-long-video' => '(fail video Ogg $1, tempoh $2, $4×$5 piksel, $3)',
	'tmh-long-multiplexed' => '(fail audio/video multipleks Ogg, $1, tempoh $2, $4×$5 piksel, keseluruhan $3)',
	'tmh-long-general' => '(fail media Ogg, tempoh $2, $3)',
	'tmh-long-error' => '(Fail Ogg tidak sah: $1)',
	'tmh-play' => 'Main',
	'tmh-pause' => 'Jeda',
	'tmh-stop' => 'Henti',
	'tmh-play-video' => 'Main video',
	'tmh-play-sound' => 'Main bunyi',
	'tmh-no-player' => 'Maaf, sistem anda tidak mempunyai perisian pemain yang disokong. Sila <a href=\\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\\">muat turun sebuah pemain</a>.',
	'tmh-no-xiphqt' => 'Anda tidak mempunyai komponen XiphQT untuk QuickTime. QuickTime tidak boleh memainkan fail Ogg tanpa komponen ini. Sila <a href=\\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\\">muat turun XiphQT</a> atau pilih pemain lain.',
	'tmh-player-videoElement' => 'Sokongan dalaman pelayar web',
	'tmh-player-oggPlugin' => 'Pemalam untuk pelayar web',
	'tmh-player-thumbnail' => 'Imej pegun sahaja',
	'tmh-player-soundthumb' => 'Tiada pemain',
	'tmh-player-selected' => '(dipilih)',
	'tmh-use-player' => 'Gunakan pemain:',
	'tmh-more' => 'Lagi…',
	'tmh-dismiss' => 'Tutup',
	'tmh-download' => 'Muat turun fail',
	'tmh-desc-link' => 'Perihal fail ini',
);

/** Erzya (Эрзянь)
 * @author Botuzhaleny-sodamo
 */
$messages['myv'] = array(
	'tmh-play' => 'Седик',
	'tmh-pause' => 'Аштевтик',
	'tmh-stop' => 'Лоткавтык',
	'tmh-play-video' => 'Нолдык видеонть',
	'tmh-play-sound' => 'Нолдык вайгеленть',
	'tmh-desc-link' => 'Те файладонть',
);

/** Nahuatl (Nāhuatl)
 * @author Fluence
 */
$messages['nah'] = array(
	'tmh-more' => 'Huehca ōmpa...',
	'tmh-download' => 'Tictemōz tlahcuilōlli',
	'tmh-desc-link' => 'Inīn tlahcuilōltechcopa',
);

/** Low German (Plattdüütsch)
 * @author Slomox
 */
$messages['nds'] = array(
	'tmh-desc' => 'Stüürprogramm för tmh-Theora- un Vorbis Datein, mitsamt en Afspeler in JavaScript',
	'tmh-short-audio' => 'tmh-$1-Toondatei, $2',
	'tmh-short-video' => 'tmh-$1-Videodatei, $2',
	'tmh-short-general' => 'tmh-$1-Mediendatei, $2',
	'tmh-long-audio' => '(tmh-$1-Toondatei, $2 lang, $3)',
	'tmh-long-video' => '(tmh-$1-Videodatei, $2 lang, $4×$5 Pixels, $3)',
	'tmh-long-multiplexed' => '(tmh-Multiplexed-Audio-/Video-Datei, $1, $2 lang, $4×$5 Pixels, $3 alltohoop)',
	'tmh-long-general' => '(tmh-Mediendatei, $2 lang, $3)',
	'tmh-long-error' => '(Kaputte tmh-Datei: $1)',
	'tmh-play' => 'Afspelen',
	'tmh-pause' => 'Paus',
	'tmh-stop' => 'Stopp',
	'tmh-play-video' => 'Video afspelen',
	'tmh-play-sound' => 'Toondatei afspelen',
	'tmh-no-player' => 'Süht so ut, as wenn dien Reekner keen passlichen Afspeler hett. Du kannst en <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Afspeler dalladen</a>.',
	'tmh-no-xiphqt' => 'Süht so ut, as wenn dien Reekner de XiphQT-Kumponent för QuickTime nich hett. Ahn dat Ding kann QuickTime keen tmh-Datein afspelen. Du kannst <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT dalladen</a> oder en annern Afspeler utwählen.',
	'tmh-player-videoElement' => 'Standard-Ünnerstüttung in’n Browser',
	'tmh-player-oggPlugin' => 'Browser-Plugin',
	'tmh-player-thumbnail' => 'blot Standbild',
	'tmh-player-soundthumb' => 'Keen Afspeler',
	'tmh-player-selected' => '(utwählt)',
	'tmh-use-player' => 'Afspeler bruken:',
	'tmh-more' => 'Mehr...',
	'tmh-dismiss' => 'Dichtmaken',
	'tmh-download' => 'Datei dalladen',
	'tmh-desc-link' => 'Över disse Datei',
);

/** Nedersaksisch (Nedersaksisch)
 * @author Servien
 */
$messages['nds-nl'] = array(
	'tmh-desc' => 'Haandelt veur Ogg Theora- en Vorbisbestanen, mit JavaScriptmediaspeuler',
	'tmh-short-audio' => 'Ogg $1 geluudsbestaand, $2',
	'tmh-short-video' => 'Ogg $1 videobestaand, $2',
	'tmh-short-general' => 'Ogg $1 mediabestaand, $2',
	'tmh-long-audio' => '(Ogg $1 geluudsbestaand, lengte $2, $3)',
	'tmh-long-video' => '(Ogg $1 videobestaand, lengte $2, $4×$5 pixels, $3)',
	'tmh-long-multiplexed' => '(Ogg emultiplexed geluuds-/videobestaand, $1, lengte $2, $4×$5 pixels, $3 totaal)',
	'tmh-long-general' => '(tmh-mediabestaand, lengte $2, $3)',
	'tmh-long-error' => '(Ongeldig tmh-bestaand: $1)',
	'tmh-play' => 'Ofspeulen',
	'tmh-pause' => 'Pauze',
	'tmh-stop' => 'Stop',
	'tmh-play-video' => 'Video ofspeulen',
	'tmh-play-sound' => 'Geluud ofspeulen',
	'tmh-no-player' => 'Joew system hef gien ondersteunende mediaspeulers.
Instelleer een <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">mediaspeuler</a>.',
	'tmh-no-xiphqt' => '\'t Lik derop da-j de compenent XiphQT veur QuickTime neet hemmen.
QuickTime kan tmh-bestanen neet ofspeulen zonder disse compenent.
Instelleer <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT</a> of kies een aandere mediaspeuler.',
	'tmh-player-videoElement' => 'Standardondersteuning in webkieker',
	'tmh-player-oggPlugin' => 'Webkiekeruutbreiding',
	'tmh-player-thumbnail' => 'Allinnig stilstaond beeld',
	'tmh-player-soundthumb' => 'Gien mediaspeuler',
	'tmh-player-selected' => '(ekeuzen)',
	'tmh-use-player' => 'Gebruuk mediaspeuler:',
	'tmh-more' => 'Meer...',
	'tmh-dismiss' => 'Sluten',
	'tmh-download' => 'Bestaand binnenhaolen',
	'tmh-desc-link' => 'Over dit bestaand',
);

/** Dutch (Nederlands)
 * @author SPQRobin
 * @author Siebrand
 */
$messages['nl'] = array(
	'tmh-desc' => 'Handelt Ogg Theora- en Vorbis-bestanden af met een JavaScript-mediaspeler',
	'tmh-short-audio' => 'Ogg $1 geluidsbestand, $2',
	'tmh-short-video' => 'Ogg $1 videobestand, $2',
	'tmh-short-general' => 'Ogg $1 mediabestand, $2',
	'tmh-long-audio' => '(Ogg $1 geluidsbestand, lengte $2, $3)',
	'tmh-long-video' => '(Ogg $1 video file, lengte $2, $4×$5 pixels, $3)',
	'tmh-long-multiplexed' => '(Ogg gemultiplexed geluids/videobestand, $1, lengte $2, $4×$5 pixels, $3 totaal)',
	'tmh-long-general' => '(Ogg mediabestand, lengte $2, $3)',
	'tmh-long-error' => '(Ongeldig tmh-bestand: $1)',
	'tmh-play' => 'Afspelen',
	'tmh-pause' => 'Pauze',
	'tmh-stop' => 'Stop',
	'tmh-play-video' => 'Video afspelen',
	'tmh-play-sound' => 'Geluid afspelen',
	'tmh-no-player' => 'Uw systeem heeft geen van de ondersteunde mediaspelers.
Installeer <a href="http://www.java.com/nl/download/manual.jsp">Java</a>.',
	'tmh-no-xiphqt' => 'Het lijkt erop dat u de component XiphQT voor QuickTime niet hebt.
QuickTime kan tmh-bestanden niet afspelen zonder deze component.
Download <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT</a> of kies een andere speler.',
	'tmh-player-videoElement' => 'Standaardondersteuning in browser',
	'tmh-player-oggPlugin' => 'Browserplugin',
	'tmh-player-thumbnail' => 'Alleen stilstaand beeld',
	'tmh-player-soundthumb' => 'Geen mediaspeler',
	'tmh-player-selected' => '(geselecteerd)',
	'tmh-use-player' => 'Gebruik speler:',
	'tmh-more' => 'Meer…',
	'tmh-dismiss' => 'Sluiten',
	'tmh-download' => 'Bestand downloaden',
	'tmh-desc-link' => 'Over dit bestand',
	'tmh-oggThumb-version' => 'OggHandler vereist oggThumb versie $1 of hoger.',
	'tmh-oggThumb-failed' => 'oggThumb kon geen miniatuur aanmaken.',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Eirik
 * @author Harald Khan
 */
$messages['nn'] = array(
	'tmh-desc' => 'Gjer at Ogg Theora- og Ogg Vorbis-filer kan verta køyrte ved hjelp av JavaScript-avspelar.',
	'tmh-short-audio' => 'Ogg $1-lydfil, $2',
	'tmh-short-video' => 'Ogg $1-videofil, $2',
	'tmh-short-general' => 'Ogg $1-mediafil, $2',
	'tmh-long-audio' => '(Ogg $1-lydfil, lengd $2, $3)',
	'tmh-long-video' => '(Ogg $1-videofil, lengd $2, $4×$5 pikslar, $3)',
	'tmh-long-multiplexed' => '(Samansett ogg lyd-/videofil, $1, lengd $2, $4×$5 pikslar, $3 til saman)',
	'tmh-long-general' => '(Ogg mediafil, lengd $2, $3)',
	'tmh-long-error' => '(Ugyldig tmh-fil: $1)',
	'tmh-play' => 'Spel av',
	'tmh-pause' => 'Pause',
	'tmh-stop' => 'Stopp',
	'tmh-play-video' => 'Spel av videofila',
	'tmh-play-sound' => 'Spel av lydfila',
	'tmh-no-player' => 'Beklagar, systemet ditt har ikkje støtta programvare til avspeling. Ver venleg og <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">last ned ein avspelar</a>.',
	'tmh-no-xiphqt' => 'Du ser ikkje ut til å ha XiphQT-komponenten til QuickTime. QuickTime kan ikkje spele av tmh-filer utan denne. Ver venleg og <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">last ned XiphQT</a> eller vel ein annan avspelar.',
	'tmh-player-videoElement' => 'Innebygd nettlesarstøtte',
	'tmh-player-oggPlugin' => 'Programtillegg for nettlesar',
	'tmh-player-thumbnail' => 'Berre stillbilete',
	'tmh-player-soundthumb' => 'Ingen avspelar',
	'tmh-player-selected' => '(valt)',
	'tmh-use-player' => 'Bruk avspelaren:',
	'tmh-more' => 'Meir...',
	'tmh-dismiss' => 'Lat att',
	'tmh-download' => 'Last ned fila',
	'tmh-desc-link' => 'Om denne fila',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 * @author Laaknor
 */
$messages['no'] = array(
	'tmh-desc' => 'Gjør at Ogg Theora- og Ogg Vorbis-filer kan kjøres med hjelp av JavaScript-avspiller.',
	'tmh-short-audio' => 'Ogg $1 lydfil, $2',
	'tmh-short-video' => 'Ogg $1 videofil, $2',
	'tmh-short-general' => 'Ogg $1 mediefil, $2',
	'tmh-long-audio' => '(Ogg $1 lydfil, lengde $2, $3)',
	'tmh-long-video' => '(Ogg $1 videofil, lengde $2, $4×$5 piksler, $3)',
	'tmh-long-multiplexed' => '(Sammensatt ogg lyd-/videofil, $1, lengde $2, $4×$5 piksler, $3 til sammen)',
	'tmh-long-general' => '(Ogg mediefil, lengde $2, $3)',
	'tmh-long-error' => '(Ugyldig tmh-fil: $1)',
	'tmh-play' => 'Spill',
	'tmh-pause' => 'Pause',
	'tmh-stop' => 'Stopp',
	'tmh-play-video' => 'Spill av video',
	'tmh-play-sound' => 'Spill av lyd',
	'tmh-no-player' => 'Beklager, systemet ditt har ingen medieavspillere som støtter filformatet. Vennligst <a href="http://mediawiki.org/wiki/Extension:OggHandler/Client_download">last ned en avspiller</a> som støtter formatet.',
	'tmh-no-xiphqt' => 'Du har ingen XiphQT-komponent for QuickTime. QuickTime kan ikke spille tmh-filer uten denne komponenten. <a href="http://mediawiki.org/wiki/Extension:OggHandler/Client_download">last ned XiphQT</a> eller velg en annen medieavspiller.',
	'tmh-player-videoElement' => 'Innebygd nettleserstøtte',
	'tmh-player-oggPlugin' => 'Programtillegg for nettleser',
	'tmh-player-thumbnail' => 'Kun stillbilder',
	'tmh-player-soundthumb' => 'Ingen medieavspiller',
	'tmh-player-selected' => '(valgt)',
	'tmh-use-player' => 'Bruk avspiller:',
	'tmh-more' => 'Mer …',
	'tmh-dismiss' => 'Lukk',
	'tmh-download' => 'Last ned fil',
	'tmh-desc-link' => 'Om denne filen',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'tmh-desc' => 'Supòrt pels fichièrs Ogg Theora e Vorbis, amb un lector Javascript',
	'tmh-short-audio' => 'Fichièr son Ogg $1, $2',
	'tmh-short-video' => 'Fichièr vidèo Ogg $1, $2',
	'tmh-short-general' => 'Fichièr mèdia Ogg $1, $2',
	'tmh-long-audio' => '(Fichièr son Ogg $1, durada $2, $3)',
	'tmh-long-video' => '(Fichièr vidèo Ogg $1, durada $2, $4×$5 pixèls, $3)',
	'tmh-long-multiplexed' => '(Fichièr multiplexat àudio/vidèo Ogg, $1, durada $2, $4×$5 pixèls, $3)',
	'tmh-long-general' => '(Fichièr mèdia Ogg, durada $2, $3)',
	'tmh-long-error' => '(Fichièr Ogg invalid : $1)',
	'tmh-play' => 'Legir',
	'tmh-pause' => 'Pausa',
	'tmh-stop' => 'Stòp',
	'tmh-play-video' => 'Legir la vidèo',
	'tmh-play-sound' => 'Legir lo son',
	'tmh-no-player' => 'O planhèm, aparentament, vòstre sistèma a pas cap de lectors suportats. Installatz <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/oc">un dels lectors suportats</a>.',
	'tmh-no-xiphqt' => 'Aparentament avètz pas lo compausant XiphQT per Quicktime. Quicktime pòt pas legir los fiquièrs Ogg sens aqueste compausant. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/fr"> Telecargatz-lo XiphQT</a> o causissètz un autre lector.',
	'tmh-player-videoElement' => 'Supòrt del navigador natiu',
	'tmh-player-oggPlugin' => 'Plugin del navigador',
	'tmh-player-thumbnail' => 'Imatge estatic solament',
	'tmh-player-soundthumb' => 'Cap de lector',
	'tmh-player-selected' => '(seleccionat)',
	'tmh-use-player' => 'Utilizar lo lector :',
	'tmh-more' => 'Mai…',
	'tmh-dismiss' => 'Tampar',
	'tmh-download' => 'Telecargar lo fichièr',
	'tmh-desc-link' => "A prepaus d'aqueste fichièr",
);

/** Ossetic (Иронау)
 * @author Amikeco
 */
$messages['os'] = array(
	'tmh-more' => 'Фылдæр…',
	'tmh-download' => 'Файл æрбавгæн',
);

/** Punjabi (ਪੰਜਾਬੀ)
 * @author Gman124
 */
$messages['pa'] = array(
	'tmh-more' => 'ਹੋਰ...',
);

/** Deitsch (Deitsch)
 * @author Xqt
 */
$messages['pdc'] = array(
	'tmh-more' => 'Mehr…',
	'tmh-download' => 'Feil runnerlaade',
);

/** Polish (Polski)
 * @author Derbeth
 * @author Leinad
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'tmh-desc' => 'Obsługa plików w formacie Ogg Theora i Vorbis z odtwarzaczem w JavaScripcie',
	'tmh-short-audio' => 'Plik dźwiękowy Ogg $1, $2',
	'tmh-short-video' => 'Plik wideo Ogg $1, $2',
	'tmh-short-general' => 'Plik multimedialny Ogg $1, $2',
	'tmh-long-audio' => '(plik dźwiękowy Ogg $1, długość $2, $3)',
	'tmh-long-video' => '(plik wideo Ogg $1, długość $2, rozdzielczość $4×$5, $3)',
	'tmh-long-multiplexed' => '(plik audio/wideo Ogg, $1, długość $2, rozdzielczość $4×$5, ogółem $3)',
	'tmh-long-general' => '(plik multimedialny Ogg, długość $2, $3)',
	'tmh-long-error' => '(niepoprawny plik Ogg: $1)',
	'tmh-play' => 'Odtwórz',
	'tmh-pause' => 'Pauza',
	'tmh-stop' => 'Stop',
	'tmh-play-video' => 'Odtwórz wideo',
	'tmh-play-sound' => 'Odtwórz dźwięk',
	'tmh-no-player' => 'W Twoim systemie brak obsługiwanego programu odtwarzacza. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/pl">Pobierz i zainstaluj odtwarzacz</a>.',
	'tmh-no-xiphqt' => 'Brak komponentu XiphQT dla programu QuickTime. QuickTime nie może odtwarzać plików Ogg bez tego komponentu. <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/pl">Pobierz XiphQT</a> lub użyj innego odtwarzacza.',
	'tmh-player-videoElement' => 'Obsługa bezpośrednio przez przeglądarkę',
	'tmh-player-oggPlugin' => 'Wtyczka do przeglądarki',
	'tmh-player-thumbnail' => 'Tylko nieruchomy obraz',
	'tmh-player-soundthumb' => 'Bez odtwarzacza',
	'tmh-player-selected' => '(wybrany)',
	'tmh-use-player' => 'Użyj odtwarzacza:',
	'tmh-more' => 'Więcej...',
	'tmh-dismiss' => 'Zamknij',
	'tmh-download' => 'Pobierz plik',
	'tmh-desc-link' => 'Właściwości pliku',
	'tmh-oggThumb-version' => 'OggHandler wymaga oggThumb w wersji $1 lub późniejszej.',
	'tmh-oggThumb-failed' => 'oggThumb nie udało się utworzyć miniaturki.',
);

/** Piedmontese (Piemontèis)
 * @author Bèrto 'd Sèra
 * @author Dragonòt
 */
$messages['pms'] = array(
	'tmh-desc' => 'Gestor për ij file Ogg Theora e Vorbis, con riprodotor JavaScript',
	'tmh-short-audio' => 'Registrassion Ogg $1, $2',
	'tmh-short-video' => 'Film Ogg $1, $2',
	'tmh-short-general' => 'Archivi Multimojen Ogg $1, $2',
	'tmh-long-audio' => "(Registrassion Ogg $1, ch'a dura $2, $3)",
	'tmh-long-video' => "(Film Ogg $1, ch'a dura $2, formà $4×$5 px, $3)",
	'tmh-long-multiplexed' => "(Archivi audio/video multiplessà Ogg, $1, ch'a dura $2, formà $4×$5 px, $3 an tut)",
	'tmh-long-general' => "(Archivi multimojen Ogg, ch'a dura $2, $3)",
	'tmh-long-error' => '(Archivi ogg nen bon: $1)',
	'tmh-play' => 'Smon',
	'tmh-pause' => 'Pàusa',
	'tmh-stop' => 'Fërma',
	'tmh-play-video' => 'Smon ël film',
	'tmh-play-sound' => 'Smon ël sonòr',
	'tmh-no-player' => "Darmagi, ma sò calcolator a smija ch'a l'abia pa gnun programa ch'a peul smon-e dj'archivi multi-mojen. Për piasì <a href=\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\">ch'as në dëscaria un</a>.",
	'tmh-no-xiphqt' => "A smija che ansima a sò calcolator a-i sia nen ël component XiphQT dël programa QuickTime. QuickTime a-i la fa pa a dovré dj'archivi an forma Ogg files s'a l'ha nen ës component-lì. Për piasì <a href=\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\">ch'as dëscaria XiphQT</a> ò pura ch'as sërna n'àotr programa për dovré j'archivi multi-mojen.",
	'tmh-player-videoElement' => 'Apògg browser nativ',
	'tmh-player-oggPlugin' => 'Spinòt (plugin) për browser',
	'tmh-player-thumbnail' => 'Mach na figurin-a fissa',
	'tmh-player-soundthumb' => 'Gnun programa për vardé/scoté',
	'tmh-player-selected' => '(selessionà)',
	'tmh-use-player' => 'Dovré ël programa:',
	'tmh-more' => 'Dë pì...',
	'tmh-dismiss' => 'sëré',
	'tmh-download' => "Dëscarié l'archivi",
	'tmh-desc-link' => "Rësgoard a st'archivi",
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'tmh-short-audio' => 'Ogg $1 غږيزه دوتنه، $2',
	'tmh-short-video' => 'Ogg $1 ويډيويي دوتنه، $2',
	'tmh-short-general' => 'Ogg $1 رسنيزه دوتنه، $2',
	'tmh-play' => 'غږول',
	'tmh-stop' => 'درول',
	'tmh-play-video' => 'ويډيو غږول',
	'tmh-play-sound' => 'غږ غږول',
	'tmh-player-videoElement' => 'د کورني کتنمل ملاتړ',
	'tmh-player-thumbnail' => 'يوازې ولاړ انځور',
	'tmh-player-soundthumb' => 'هېڅ کوم غږونکی نه',
	'tmh-player-selected' => '(ټاکل شوی)',
	'tmh-use-player' => 'غږونکی کارول:',
	'tmh-more' => 'نور...',
	'tmh-dismiss' => 'تړل',
	'tmh-download' => 'دوتنه ښکته کول',
	'tmh-desc-link' => 'د همدې دوتنې په اړه',
);

/** Portuguese (Português)
 * @author 555
 * @author Hamilton Abreu
 * @author Malafaya
 * @author Waldir
 */
$messages['pt'] = array(
	'tmh-desc' => 'Manuseador para ficheiros Ogg Theora e Vorbis, com reprodutor JavaScript',
	'tmh-short-audio' => 'Áudio Ogg $1, $2',
	'tmh-short-video' => 'Vídeo Ogg $1, $2',
	'tmh-short-general' => 'Multimédia Ogg $1, $2',
	'tmh-long-audio' => '(Áudio Ogg $1, $2 de duração, $3)',
	'tmh-long-video' => '(Vídeo Ogg $1, $2 de duração, $4×$5 pixels, $3)',
	'tmh-long-multiplexed' => '(Áudio/vídeo Ogg multifacetado, $1, $2 de duração, $4×$5 pixels, $3 no todo)',
	'tmh-long-general' => '(Multimédia Ogg, $2 de duração, $3)',
	'tmh-long-error' => '(Ficheiro ogg inválido: $1)',
	'tmh-play' => 'Reproduzir',
	'tmh-pause' => 'Pausar',
	'tmh-stop' => 'Parar',
	'tmh-play-video' => 'Reproduzir vídeo',
	'tmh-play-sound' => 'Reproduzir som',
	'tmh-no-player' => "Desculpe, mas o seu sistema não aparenta ter qualquer leitor suportado. Por favor, faça o <a href=\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\">''download'' de um leitor</a>.",
	'tmh-no-xiphqt' => "Aparentemente não tem o componente XiphQT do QuickTime.
O QuickTime não pode reproduzir ficheiros Ogg sem este componente.
Por favor, faça o <a href=\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\">''download'' do XiphQT</a> ou escolha outro leitor.",
	'tmh-player-videoElement' => 'Suporte nativo do browser',
	'tmh-player-oggPlugin' => "''Plugin'' do browser",
	'tmh-player-thumbnail' => 'Apenas imagem estática',
	'tmh-player-soundthumb' => 'Sem player',
	'tmh-player-selected' => '(selecionado)',
	'tmh-use-player' => 'Usar player:',
	'tmh-more' => 'Mais...',
	'tmh-dismiss' => 'Fechar',
	'tmh-download' => 'Fazer download do ficheiro',
	'tmh-desc-link' => 'Sobre este ficheiro',
	'tmh-oggThumb-version' => 'O oggHandler requer o oggThumb versão $1 ou posterior.',
	'tmh-oggThumb-failed' => 'O oggThumb não conseguiu criar a miniatura.',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Eduardo.mps
 */
$messages['pt-br'] = array(
	'tmh-desc' => 'Manipulador para arquivos Ogg Theora e Vorbis, com reprodutor JavaScript',
	'tmh-short-audio' => 'Arquivo de áudio Ogg $1, $2',
	'tmh-short-video' => 'Arquivo de vídeo Ogg $1, $2',
	'tmh-short-general' => 'Arquivo multimídia Ogg $1, $2',
	'tmh-long-audio' => '(Arquivo de Áudio Ogg $1, $2 de duração, $3)',
	'tmh-long-video' => '(Vídeo Ogg $1, $2 de duração, $4×$5 pixels, $3)',
	'tmh-long-multiplexed' => '(Áudio/vídeo Ogg multifacetado, $1, $2 de duração, $4×$5 pixels, $3 no todo)',
	'tmh-long-general' => '(Multimídia Ogg, $2 de duração, $3)',
	'tmh-long-error' => '(Ficheiro ogg inválido: $1)',
	'tmh-play' => 'Reproduzir',
	'tmh-pause' => 'Pausar',
	'tmh-stop' => 'Parar',
	'tmh-play-video' => 'Reproduzir vídeo',
	'tmh-play-sound' => 'Reproduzir som',
	'tmh-no-player' => 'Lamentamos, mas seu sistema aparenta não ter um reprodutor suportado. Por gentileza, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">faça o download de um reprodutor</a>.',
	'tmh-no-xiphqt' => 'Aparentemente você não tem o componente XiphQT para QuickTime. Não será possível reproduzir arquivos Ogg pelo QuickTime sem tal componente. Por gentileza, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">faça o descarregamento do XiphQT</a> ou escolha outro reprodutor.',
	'tmh-player-videoElement' => 'Suporte interno do navegador',
	'tmh-player-oggPlugin' => 'Plugin do navegador',
	'tmh-player-thumbnail' => 'Apenas imagem estática',
	'tmh-player-soundthumb' => 'Sem reprodutor',
	'tmh-player-selected' => '(selecionado)',
	'tmh-use-player' => 'Usar reprodutor:',
	'tmh-more' => 'Mais...',
	'tmh-dismiss' => 'Fechar',
	'tmh-download' => 'Descarregar arquivo',
	'tmh-desc-link' => 'Sobre este arquivo',
);

/** Quechua (Runa Simi)
 * @author AlimanRuna
 */
$messages['qu'] = array(
	'tmh-play' => 'Waqachiy',
	'tmh-pause' => "P'itiy",
	'tmh-stop' => 'Tukuchiy',
	'tmh-play-video' => 'Widyuta rikuchiy',
	'tmh-play-sound' => 'Ruqyayta uyarichiy',
	'tmh-player-soundthumb' => 'Manam waqachiqchu',
	'tmh-player-selected' => '(akllasqa)',
	'tmh-use-player' => "Kay waqachiqta llamk'achiy:",
	'tmh-more' => 'Astawan...',
	'tmh-dismiss' => "Wichq'ay",
	'tmh-download' => 'Willañiqita chaqnamuy',
	'tmh-desc-link' => 'Kay willañiqimanta',
);

/** Romanian (Română)
 * @author KlaudiuMihaila
 * @author Mihai
 * @author Stelistcristi
 */
$messages['ro'] = array(
	'tmh-short-audio' => 'Fişier de sunet ogg $1, $2',
	'tmh-short-video' => 'Fişier video ogg $1, $2',
	'tmh-short-general' => 'Fişier media ogg $1, $2',
	'tmh-long-audio' => '(Fişier de sunet ogg $1, lungime $2, $3)',
	'tmh-long-video' => '(Fişier video ogg $1, lungime $2, $4×$5 pixeli, $3)',
	'tmh-long-multiplexed' => '(Fişier multiplexat audio/video ogg, $1, lungime $2, $4×$5 pixeli, $3)',
	'tmh-long-general' => '(Fişier media ogg, lungime $2, $3)',
	'tmh-long-error' => '(Fişier ogg incorect: $1)',
	'tmh-play' => 'Redă',
	'tmh-pause' => 'Pauză',
	'tmh-stop' => 'Stop',
	'tmh-play-video' => 'Redă video',
	'tmh-play-sound' => 'Redă sunet',
	'tmh-no-player' => 'Îmi pare rău, sistemul tău nu pare să aibă vreun program de redare suportat.
Te rog <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">descarcă un program de redare</a>.',
	'tmh-player-videoElement' => 'Navigator cu suport nativ',
	'tmh-player-oggPlugin' => 'Insert navigator',
	'tmh-player-thumbnail' => 'Încă imaginea doar',
	'tmh-player-soundthumb' => 'Niciun program de redare',
	'tmh-player-selected' => '(selectat)',
	'tmh-use-player' => 'Foloseşte programul de redare:',
	'tmh-more' => 'Mai mult…',
	'tmh-dismiss' => 'Închide',
	'tmh-download' => 'Descarcă fişier',
	'tmh-desc-link' => 'Despre acest fişier',
);

/** Tarandíne (Tarandíne)
 * @author Joetaras
 */
$messages['roa-tara'] = array(
	'tmh-desc' => "Gestore pe le file Ogg Theora e Vorbis, cu 'nu programme de riproduzione JavaScript",
	'tmh-short-audio' => 'File audie Ogg $1, $2',
	'tmh-short-video' => 'File video Ogg $1, $2',
	'tmh-short-general' => 'File media Ogg $1, $2',
	'tmh-long-audio' => '(File audie Ogg $1, lunghezze $2, $3)',
	'tmh-long-video' => '(File video Ogg $1, lunghezze $2, $4 x $5 pixel, $3)',
	'tmh-long-multiplexed' => '(File multiplexed audie e video Ogg $1, lunghezze $2, $4 x $5 pixel, $3 in totale)',
	'tmh-long-general' => '(File media Ogg, lunghezze $2, $3)',
	'tmh-long-error' => '(Ogg file invalide: $1)',
	'tmh-play' => 'Riproduce',
	'tmh-pause' => 'Mitte in pause',
	'tmh-stop' => 'Stuèppe',
	'tmh-play-video' => "Riproduce 'u video",
	'tmh-play-sound' => 'Riproduce le suène',
	'tmh-no-player' => "Ne dispiace, 'u sisteme tune pare ca non ge tène nisciune softuare p'a riproduzione.<br />
Pe piacere, <a href=\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\">scareche 'u reproduttore</a>.",
	'tmh-no-xiphqt' => "Non ge pare ca tìne 'u combonende XiphQT pu QuickTime.<br />
QuickTime non ge pò reproducere file Ogg senze stu combonende.<br />
Pe piacere <a href=\"http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download\">scareche XiphQT</a> o scacchie 'n'otre reproduttore.",
	'tmh-player-videoElement' => 'Supporte browser native',
	'tmh-player-oggPlugin' => "Plugin d'u browser",
	'tmh-player-thumbnail' => 'Angore sulamende immaggine',
	'tmh-player-soundthumb' => 'Nisciune reproduttore',
	'tmh-player-selected' => '(scacchiate)',
	'tmh-use-player' => "Ause 'u reproduttore:",
	'tmh-more' => 'De cchiù...',
	'tmh-dismiss' => 'Chiude',
	'tmh-download' => 'Scareche stu file',
	'tmh-desc-link' => "'Mbormaziune sus a stu file",
);

/** Russian (Русский)
 * @author Ahonc
 * @author Kv75
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'tmh-desc' => 'Обработчик файлов Ogg Theora и Vorbis с использованием JavaScript-проигрывателя',
	'tmh-short-audio' => 'Звуковой файл Ogg $1, $2',
	'tmh-short-video' => 'Видео-файл Ogg $1, $2',
	'tmh-short-general' => 'Медиа-файл Ogg $1, $2',
	'tmh-long-audio' => '(звуковой файл Ogg $1, длина $2, $3)',
	'tmh-long-video' => '(видео-файл Ogg $1, длина $2, $4×$5 пикселов, $3)',
	'tmh-long-multiplexed' => '(мультиплексный аудио/видео-файл Ogg, $1, длина $2, $4×$5 пикселов, $3 всего)',
	'tmh-long-general' => '(медиа-файл Ogg, длина $2, $3)',
	'tmh-long-error' => '(неправильный tmh-файл: $1)',
	'tmh-play' => 'Воспроизвести',
	'tmh-pause' => 'Пауза',
	'tmh-stop' => 'Остановить',
	'tmh-play-video' => 'Воспроизвести видео',
	'tmh-play-sound' => 'Воспроизвести звук',
	'tmh-no-player' => 'Извините, ваша система не имеет необходимого программного обеспечение для воспроизведения файлов. Пожалуйста, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">скачайте проигрыватель</a>.',
	'tmh-no-xiphqt' => 'Отсутствует компонент XiphQT для QuickTime. QuickTime не может воспроизвести файл Ogg без этого компонента. Пожалуйста, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">скачайте XiphQT</a> или выберите другой проигрыватель.',
	'tmh-player-videoElement' => 'Встроенная поддержка браузером',
	'tmh-player-oggPlugin' => 'Ogg модуль',
	'tmh-player-thumbnail' => 'Только неподвижное изображение',
	'tmh-player-soundthumb' => 'Нет проигрывателя',
	'tmh-player-selected' => '(выбран)',
	'tmh-use-player' => 'Использовать проигрыватель:',
	'tmh-more' => 'Больше…',
	'tmh-dismiss' => 'Скрыть',
	'tmh-download' => 'Загрузить файл',
	'tmh-desc-link' => 'Информация об этом файле',
	'tmh-oggThumb-version' => 'OggHandler требует oggThumb версии $1 или более поздней.',
	'tmh-oggThumb-failed' => 'oggThumb не удалось создать миниатюру.',
);

/** Yakut (Саха тыла)
 * @author HalanTul
 */
$messages['sah'] = array(
	'tmh-desc' => 'Обработчик файлов Ogg Theora и Vorbis с использованием JavaScript-проигрывателя',
	'tmh-short-audio' => 'Звуковой файл Ogg $1, $2',
	'tmh-short-video' => 'Видео-файл Ogg $1, $2',
	'tmh-short-general' => 'Медиа-файл Ogg $1, $2',
	'tmh-long-audio' => '(звуковой файл Ogg $1, уһуна $2, $3)',
	'tmh-long-video' => '(видео-файл Ogg $1, уһуна $2, $4×$5 пииксэллээх, $3)',
	'tmh-long-multiplexed' => '(мультиплексный аудио/видео-файл Ogg, $1, уһуна $2, $4×$5 пииксэллээх, барыта $3)',
	'tmh-long-general' => '(медиа-файл Ogg, уһуна $2, $3)',
	'tmh-long-error' => '(сыыһа tmh-файл: $1)',
	'tmh-play' => 'Оонньот',
	'tmh-pause' => 'Тохтото түс',
	'tmh-stop' => 'Тохтот',
	'tmh-play-video' => 'Көрдөр',
	'tmh-play-sound' => 'Иһитиннэр',
	'tmh-no-player' => 'Хомойуох иһин эн систиэмэҕэр иһитиннэрэр/көрдөрөр анал бырагырааммалар суохтар эбит. Бука диэн, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">плееры хачайдан</a>.',
	'tmh-no-xiphqt' => 'QuickTime маннык тэрээбэтэ: XiphQT суох эбит. Онон QuickTime бу Ogg билэни (файлы) оонньотор кыаҕа суох. Бука диэн, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download"> XiphQT хачайдан</a> эбэтэр атын плееры тал.',
	'tmh-player-videoElement' => 'Браузер бэйэтин өйөөһүнэ',
	'tmh-player-oggPlugin' => 'Браузер плагина',
	'tmh-player-thumbnail' => 'Хамсаабат ойууну эрэ',
	'tmh-player-soundthumb' => 'Плеер суох',
	'tmh-player-selected' => '(талыллыбыт)',
	'tmh-use-player' => 'Бу плееры туттарга:',
	'tmh-more' => 'Өссө...',
	'tmh-dismiss' => 'Кистээ/сап',
	'tmh-download' => 'Билэни хачайдаа',
	'tmh-desc-link' => 'Бу билэ туһунан',
);

/** Sinhala (සිංහල)
 * @author නන්දිමිතුරු
 */
$messages['si'] = array(
	'tmh-desc' => 'Ogg Theora සහ Vorbis ගොනු සඳහා හසුරුවනය, ජාවාස්ක්‍රිප්ට් ප්ලේයර් සමඟ',
	'tmh-short-audio' => 'Ogg $1 ශ්‍රව්‍ය ගොනුව, $2',
	'tmh-short-video' => 'Ogg $1 දෘශ්‍ය ගොනුව, $2',
	'tmh-short-general' => 'Ogg $1 මාධ්‍ය ගොනුව, $2',
	'tmh-long-audio' => '(Ogg $1 ශ්‍රව්‍ය ගොනුව, ප්‍රවර්තනය $2, $3)',
	'tmh-long-video' => '(Ogg $1 දෘශ්‍ය ගොනුව, ප්‍රවර්තනය $2, $4×$5 පික්සල්, $3)',
	'tmh-long-multiplexed' => '(Ogg බහුපථකාරක ශ්‍රව්‍ය/දෘශ්‍ය ගොනුව, $1, ප්‍රවර්තනය $2, $4×$5 පික්සල්, $3 සමස්ත)',
	'tmh-long-general' => '(Ogg මාධ්‍ය ගොනුව, ප්‍රවර්තනය $2, $3)',
	'tmh-long-error' => '(අනීතික ogg ගොනුව: $1)',
	'tmh-play' => 'වාදනය කරන්න',
	'tmh-pause' => 'විරාම කරන්න',
	'tmh-stop' => 'නවතන්න',
	'tmh-play-video' => 'දෘශ්‍ය වාදනය කරන්න',
	'tmh-play-sound' => 'ශබ්දය වාදනය කරන්න',
	'tmh-no-player' => 'කණගාටුයි, කිසිම සහායක ධාවක මෘදුකාංගයක් ඔබ පද්ධතිය සතුව ඇති බවක් නොපෙනේ.
කරුණාකර <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ධාවකයක් බා ගන්න</a>.',
	'tmh-no-xiphqt' => 'QuickTime සඳහා XiphQT සංරචකය ඔබ සතුව ඇති බවක් නොපෙනේ.
මෙම සංරචකය නොමැතිව Ogg ගොනු ධාවනය කිරීම  QuickTime විසින් සිදුකල නොහැක.
කරුණාකර <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download"> XiphQT බා ගන්න</a> නැතහොත් වෙනත් ධාවකයක් තෝරාගන්න.',
	'tmh-player-oggPlugin' => 'බ්‍රවුසර ප්ලගිත',
	'tmh-player-cortado' => 'Cortado (ජාවා)',
	'tmh-player-thumbnail' => 'නිශ්චල රූප පමණි',
	'tmh-player-soundthumb' => 'ධාවකයක් නොමැත',
	'tmh-player-selected' => '(තෝරාගෙන)',
	'tmh-use-player' => 'ධාවකය භාවිතා කරන්න:',
	'tmh-more' => 'ඉතිරිය…',
	'tmh-dismiss' => 'වසන්න',
	'tmh-download' => 'ගොනුව බා ගන්න',
	'tmh-desc-link' => 'මෙම ගොනුව පිළිබඳ',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'tmh-desc' => 'Obsluha súborov Ogg Theora a Vorbis s JavaScriptovým prehrávačom',
	'tmh-short-audio' => 'Zvukový súbor ogg $1, $2',
	'tmh-short-video' => 'Video súbor ogg $1, $2',
	'tmh-short-general' => 'Multimediálny súbor ogg $1, $2',
	'tmh-long-audio' => '(Zvukový súbor ogg $1, dĺžka $2, $3)',
	'tmh-long-video' => '(Video súbor ogg $1, dĺžka $2, $4×$5 pixelov, $3)',
	'tmh-long-multiplexed' => '(Multiplexovaný zvukový/video súbor ogg, $1, dĺžka $2, $4×$5 pixelov, $3 celkom)',
	'tmh-long-general' => '(Multimediálny súbor ogg, dĺžka $2, $3)',
	'tmh-long-error' => '(Neplatný súbor ogg: $1)',
	'tmh-play' => 'Prehrať',
	'tmh-pause' => 'Pozastaviť',
	'tmh-stop' => 'Zastaviť',
	'tmh-play-video' => 'Prehrať video',
	'tmh-play-sound' => 'Prehrať zvuk',
	'tmh-no-player' => 'Prepáčte, zdá sa, že váš systém nemá žiadny podporovaný softvér na prehrávanie. Prosím, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">stiahnite si prehrávač</a>.',
	'tmh-no-xiphqt' => 'Zdá sa, že nemáte komponent QuickTime XiphQT. QuickTime nedokáže prehrávať ogg súbory bez tohto komponentu. Prosím, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">stiahnite si XiphQT</a> alebo si vyberte iný prehrávač.',
	'tmh-player-videoElement' => 'Natívna podpora prehliadača',
	'tmh-player-oggPlugin' => 'Zásuvný modul prehliadača',
	'tmh-player-thumbnail' => 'iba nepohyblivý obraz',
	'tmh-player-soundthumb' => 'žiadny prehrávač',
	'tmh-player-selected' => '(vybraný)',
	'tmh-use-player' => 'Použiť prehrávač:',
	'tmh-more' => 'viac...',
	'tmh-dismiss' => 'Zatvoriť',
	'tmh-download' => 'Stiahnuť súbor',
	'tmh-desc-link' => 'O tomto súbore',
);

/** Slovenian (Slovenščina)
 * @author Dbc334
 */
$messages['sl'] = array(
	'tmh-play' => 'Predvajaj',
	'tmh-pause' => 'Pavza',
	'tmh-stop' => 'Ustavi',
	'tmh-play-video' => 'Predvajaj video',
	'tmh-play-sound' => 'Predvajaj zvok',
	'tmh-player-videoElement' => 'Vgrajena podpora brskalnika',
	'tmh-player-thumbnail' => 'Samo stoječa slika',
	'tmh-player-soundthumb' => 'Brez predvajalnika',
	'tmh-player-selected' => '(izbrano)',
	'tmh-use-player' => 'Uporabi predvajalnik:',
	'tmh-more' => 'Več ...',
	'tmh-dismiss' => 'Zapri',
	'tmh-download' => 'Prenesi datoteko',
	'tmh-desc-link' => 'O datoteki',
);

/** Albanian (Shqip)
 * @author Dori
 */
$messages['sq'] = array(
	'tmh-short-audio' => 'Skedë zanore Ogg $1, $2',
	'tmh-short-video' => 'Skedë pamore Ogg $1, $2',
	'tmh-short-general' => 'Skedë mediatike Ogg $1, $2',
	'tmh-long-audio' => '(Skedë zanore Ogg $1, kohëzgjatja $2, $3)',
	'tmh-long-video' => '(Skedë pamore Ogg $1, kohëzgjatja $2, $4×$5 pixel, $3)',
	'tmh-play' => 'Fillo',
	'tmh-pause' => 'Pusho',
	'tmh-stop' => 'Ndalo',
	'tmh-play-video' => 'Fillo videon',
	'tmh-play-sound' => 'Fillo zërin',
	'tmh-no-player' => 'Ju kërkojmë ndjesë por sistemi juaj nuk ka mundësi për të kryer këtë veprim. Mund të <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">shkarkoni një mjet</a> tjetër.',
	'tmh-more' => 'Më shumë...',
	'tmh-dismiss' => 'Mbylle',
	'tmh-download' => 'Shkarko skedën',
	'tmh-desc-link' => 'Rreth kësaj skede',
);

/** Serbian Cyrillic ekavian (Српски (ћирилица))
 * @author Millosh
 * @author Sasa Stefanovic
 * @author Михајло Анђелковић
 */
$messages['sr-ec'] = array(
	'tmh-desc' => 'Руковаоц ogg Теора и Ворбис фајловима са јаваскрипт плејером',
	'tmh-short-audio' => 'Ogg $1 звучни фајл, $2.',
	'tmh-short-video' => 'Ogg $1 видео фајл, $2.',
	'tmh-short-general' => 'Ogg $1 медијски фајл, $2.',
	'tmh-long-audio' => '(Ogg $1 звучни фајл, дужина $2, $3.)',
	'tmh-long-video' => '(Ogg $1 видео фајл, дужина $2, $4×$5 пиксела, $3.)',
	'tmh-long-multiplexed' => '(Ogg мултиплексовани аудио/видео фајл, $1, дужина $2, $4×$5 пиксела, $3 укупно.)',
	'tmh-long-general' => '(Ogg медијски фајл, дужина $2, $3.)',
	'tmh-long-error' => '(Лош ogg фајл: $1.)',
	'tmh-play' => 'Пусти',
	'tmh-pause' => 'Пауза',
	'tmh-stop' => 'Стоп',
	'tmh-play-video' => 'Пусти видео',
	'tmh-play-sound' => 'Пусти звук',
	'tmh-player-videoElement' => 'Уграђена подршка у браузер',
	'tmh-player-oggPlugin' => 'Плагин за браузер',
	'tmh-player-thumbnail' => 'још увек само слика',
	'tmh-player-soundthumb' => 'нема плејера',
	'tmh-player-selected' => '(означено)',
	'tmh-use-player' => 'Користи плејер:',
	'tmh-more' => 'Више...',
	'tmh-dismiss' => 'Затвори',
	'tmh-download' => 'Преузми фајл',
	'tmh-desc-link' => 'О овом фајлу',
);

/** Serbian Latin ekavian (Srpski (latinica))
 * @author Michaello
 */
$messages['sr-el'] = array(
	'tmh-desc' => 'Rukovaoc ogg Teora i Vorbis fajlovima sa javaskript plejerom',
	'tmh-short-audio' => 'Ogg $1 zvučni fajl, $2.',
	'tmh-short-video' => 'Ogg $1 video fajl, $2.',
	'tmh-short-general' => 'Ogg $1 medijski fajl, $2.',
	'tmh-long-audio' => '(Ogg $1 zvučni fajl, dužina $2, $3.)',
	'tmh-long-video' => '(Ogg $1 video fajl, dužina $2, $4×$5 piksela, $3.)',
	'tmh-long-multiplexed' => '(Ogg multipleksovani audio/video fajl, $1, dužina $2, $4×$5 piksela, $3 ukupno.)',
	'tmh-long-general' => '(Ogg medijski fajl, dužina $2, $3.)',
	'tmh-long-error' => '(Loš ogg fajl: $1.)',
	'tmh-play' => 'Pusti',
	'tmh-pause' => 'Pauza',
	'tmh-stop' => 'Stop',
	'tmh-play-video' => 'Pusti video',
	'tmh-play-sound' => 'Pusti zvuk',
	'tmh-player-videoElement' => 'Ugrađena podrška u brauzer',
	'tmh-player-oggPlugin' => 'Plagin za brauzer',
	'tmh-player-thumbnail' => 'još uvek samo slika',
	'tmh-player-soundthumb' => 'nema plejera',
	'tmh-player-selected' => '(označeno)',
	'tmh-use-player' => 'Koristi plejer:',
	'tmh-more' => 'Više...',
	'tmh-dismiss' => 'Zatvori',
	'tmh-download' => 'Preuzmi fajl',
	'tmh-desc-link' => 'O ovom fajlu',
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'tmh-desc' => 'Stjuurengsprogramm foar Ogg Theora- un Vorbis-Doatäie, inklusive n JavaScript-Ouspielsoftware',
	'tmh-short-audio' => 'tmh-$1-Audiodoatäi, $2',
	'tmh-short-video' => 'tmh-$1-Videodoatäi, $2',
	'tmh-short-general' => 'tmh-$1-Mediadoatäi, $2',
	'tmh-long-audio' => '(tmh-$1-Audiodoatäi, Loangte: $2, $3)',
	'tmh-long-video' => '(tmh-$1-Videodoatäi, Loangte: $2, $4×$5 Pixel, $3)',
	'tmh-long-multiplexed' => '(tmh-Audio-/Video-Doatäi, $1, Loangte: $2, $4×$5 Pixel, $3)',
	'tmh-long-general' => '(tmh-Mediadoatäi, Loangte: $2, $3)',
	'tmh-long-error' => '(Uungultige tmh-Doatäi: $1)',
	'tmh-play' => 'Start',
	'tmh-pause' => 'Pause',
	'tmh-stop' => 'Stop',
	'tmh-play-video' => 'Video ouspielje',
	'tmh-play-sound' => 'Audio ouspielje',
	'tmh-no-player' => 'Dien System schient uur neen Ouspielsoftware tou ferföigjen. Installier <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ne Ouspielsoftware</a>.',
	'tmh-no-xiphqt' => 'Dien System schient nit uur ju XiphQT-Komponente foar QuickTime tou ferföigjen. QuickTime kon sunner disse Komponente neen tmh-Doatäie ouspielje.
Dou <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">leede XiphQT</a> of wääl ne uur Ouspielsoftware.',
	'tmh-player-videoElement' => 'Anweesende Browser-Unnerstutsenge',
	'tmh-player-oggPlugin' => 'Browser-Plugin',
	'tmh-player-thumbnail' => 'Wies Foarschaubielde',
	'tmh-player-soundthumb' => 'Naan Player',
	'tmh-player-selected' => '(uutwääld)',
	'tmh-use-player' => 'Ouspielsoftware:',
	'tmh-more' => 'Optione …',
	'tmh-dismiss' => 'Sluute',
	'tmh-download' => 'Doatäi spiekerje',
	'tmh-desc-link' => 'Uur disse Doatäi',
);

/** Sundanese (Basa Sunda)
 * @author Kandar
 */
$messages['su'] = array(
	'tmh-short-audio' => 'Koropak sora $1 ogg, $2',
	'tmh-short-video' => 'Koropak vidéo $1 ogg, $2',
	'tmh-short-general' => 'Koropak média $1 ogg, $2',
	'tmh-long-audio' => '(Koropak sora $1 ogg, lilana $2, $3)',
	'tmh-long-video' => '(Koropak vidéo $1 ogg, lilana $2, $4×$5 piksel, $3)',
	'tmh-long-multiplexed' => '(Koropak sora/vidéo ogg multipléks, $1, lilana $2, $4×$5 piksel, $3 gembleng)',
	'tmh-long-general' => '(Koropak média ogg, lilana $2, $3)',
	'tmh-long-error' => '(Koropak ogg teu valid: $1)',
	'tmh-play' => 'Setél',
	'tmh-pause' => 'Eureun',
	'tmh-stop' => 'Anggeusan',
	'tmh-play-video' => 'Setél vidéo',
	'tmh-play-sound' => 'Setél sora',
	'tmh-player-oggPlugin' => 'Plugin ogg',
	'tmh-player-thumbnail' => 'Gambar statis wungkul',
	'tmh-player-selected' => '(pinilih)',
	'tmh-use-player' => 'Paké panyetél:',
	'tmh-more' => 'Lianna...',
	'tmh-dismiss' => 'Tutup',
	'tmh-download' => 'Bedol',
	'tmh-desc-link' => 'Ngeunaan ieu koropak',
);

/** Swedish (Svenska)
 * @author Jon Harald Søby
 * @author Lejonel
 * @author Rotsee
 * @author Skalman
 */
$messages['sv'] = array(
	'tmh-desc' => 'Stöder filtyperna Ogg Theora och Ogg Vorbis med en JavaScript-baserad mediaspelare',
	'tmh-short-audio' => 'Ogg $1 ljudfil, $2',
	'tmh-short-video' => 'Ogg $1 videofil, $2',
	'tmh-short-general' => 'Ogg $1 mediafil, $2',
	'tmh-long-audio' => '(Ogg $1 ljudfil, längd $2, $3)',
	'tmh-long-video' => '(Ogg $1 videofil, längd $2, $4×$5 pixel, $3)',
	'tmh-long-multiplexed' => '(Ogg multiplexad ljud/video-fil, $1, längd $2, $4×$5 pixel, $3 totalt)',
	'tmh-long-general' => '(Ogg mediafil, längd $2, $3)',
	'tmh-long-error' => '(Felaktig tmh-fil: $1)',
	'tmh-play' => 'Spela upp',
	'tmh-pause' => 'Pausa',
	'tmh-stop' => 'Stoppa',
	'tmh-play-video' => 'Spela upp video',
	'tmh-play-sound' => 'Spela upp ljud',
	'tmh-no-player' => 'Tyvärr verkar det inte finnas någon mediaspelare som stöds installerad i ditt system. Det finns <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">spelare att ladda ner</a>.',
	'tmh-no-xiphqt' => 'Du verkar inte ha XiphQT-komponenten för QuickTime. Utan den kan inte QuickTime spela upp tmh-filer.Du kan <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ladda ner XiphQT</a> eller välja någon annan spelare.',
	'tmh-player-videoElement' => '<video>-element',
	'tmh-player-oggPlugin' => 'tmh-plugin',
	'tmh-player-thumbnail' => 'Endast stillbilder',
	'tmh-player-soundthumb' => 'Ingen spelare',
	'tmh-player-selected' => '(vald)',
	'tmh-use-player' => 'Välj mediaspelare:',
	'tmh-more' => 'Mer...',
	'tmh-dismiss' => 'Stäng',
	'tmh-download' => 'Ladda ner filen',
	'tmh-desc-link' => 'Om filen',
);

/** Telugu (తెలుగు)
 * @author Kiranmayee
 * @author Veeven
 * @author వైజాసత్య
 */
$messages['te'] = array(
	'tmh-short-audio' => 'Ogg $1 శ్రావ్యక ఫైలు, $2',
	'tmh-short-video' => 'Ogg $1 వీడియో ఫైలు, $2',
	'tmh-short-general' => 'Ogg $1 మీడియా ఫైలు, $2',
	'tmh-long-audio' => '(Ogg $1 శ్రవణ ఫైలు, నిడివి $2, $3)',
	'tmh-long-video' => '(Ogg $1 వీడియో ఫైలు, నిడివి $2, $4×$5 పిక్సెళ్ళు, $3)',
	'tmh-long-multiplexed' => '(ఓగ్ మల్టిప్లెక్సుడ్ శ్రవణ/దృశ్యక ఫైలు, $1, నిడివి $2, $4×$5 పిక్సెళ్ళు, $3 మొత్తం)',
	'tmh-long-general' => '(Ogg మీడియా ఫైలు, నిడివి $2, $3)',
	'tmh-long-error' => '(తప్పుడు ogg ఫైలు: $1)',
	'tmh-play' => 'ఆడించు',
	'tmh-pause' => 'ఆపు',
	'tmh-stop' => 'ఆపివేయి',
	'tmh-play-video' => 'వీడియోని ఆడించు',
	'tmh-play-sound' => 'శబ్ధాన్ని వినిపించు',
	'tmh-player-videoElement' => 'విహారిణిలో సహజాత తోడ్పాటు',
	'tmh-player-oggPlugin' => 'బ్రౌజరు ప్లగిన్',
	'tmh-player-thumbnail' => 'నిచ్చల చిత్రాలు మాత్రమే',
	'tmh-player-soundthumb' => 'ప్లేయర్ లేదు',
	'tmh-player-selected' => '(ఎంచుకున్నారు)',
	'tmh-use-player' => 'ప్లేయర్ ఉపయోగించు:',
	'tmh-more' => 'మరిన్ని...',
	'tmh-dismiss' => 'మూసివేయి',
	'tmh-download' => 'ఫైలుని దిగుమతి చేసుకోండి',
	'tmh-desc-link' => 'ఈ ఫైలు గురించి',
);

/** Tajik (Cyrillic) (Тоҷикӣ (Cyrillic))
 * @author Ibrahim
 */
$messages['tg-cyrl'] = array(
	'tmh-desc' => 'Ба дастгирандае барои парвандаҳои  Ogg Theora ва Vorbis, бо пахшкунандаи JavaScript',
	'tmh-short-audio' => 'Ogg $1 парвандаи савтӣ, $2',
	'tmh-short-video' => 'Ogg $1 парвандаи наворӣ, $2',
	'tmh-short-general' => 'Ogg $1 парвандаи расона, $2',
	'tmh-long-audio' => '(Ogg $1 парвандаи савтӣ, тӯл $2, $3)',
	'tmh-long-video' => '(Ogg $1 парвандаи наворӣ, тӯл $2, $4×$5 пикселҳо, $3)',
	'tmh-long-multiplexed' => '(Парвандаи Ogg савтӣ/наворӣ печида, $1, тӯл $2, $4×$5 пикселҳо, дар маҷмӯъ $3)',
	'tmh-long-general' => '(Парвандаи расонаи Ogg, тӯл $2, $3)',
	'tmh-long-error' => '(Парвандаи ғайримиҷози ogg: $1)',
	'tmh-play' => 'Пахш',
	'tmh-pause' => 'Сукут',
	'tmh-stop' => 'Қатъ',
	'tmh-play-video' => 'Пахши навор',
	'tmh-play-sound' => 'Пахши овоз',
	'tmh-no-player' => 'Бубахшед, дастгоҳи шумо нармафзори пахшкунандаи муносибе надорад. Лутфан <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">як барномаи пахшкунандаро боргирӣ кунед</a>.',
	'tmh-no-xiphqt' => 'Афзунаи XiphQT барои QuickTime ба назар намерасад. QuickTime наметавонад бидуни ин афзуна парвандаҳои tmh-ро пахш кунад. Лутфан <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT-ро боргирӣ кунед</a>  ё дигар нармафзори пахшкунандаро интихоб намоед.',
	'tmh-player-videoElement' => 'унсури <наворӣ>',
	'tmh-player-oggPlugin' => 'Афзунаи ogg',
	'tmh-player-thumbnail' => 'Фақат акс ҳанӯз',
	'tmh-player-soundthumb' => 'Пахшкунанда нест',
	'tmh-player-selected' => '(интихобшуда)',
	'tmh-use-player' => 'Истифода аз пахшкунанда:',
	'tmh-more' => 'Бештар...',
	'tmh-dismiss' => 'Бастан',
	'tmh-download' => 'Боргирии парванда',
	'tmh-desc-link' => 'Дар бораи ин парванда',
);

/** Tajik (Latin) (Тоҷикӣ (Latin))
 * @author Liangent
 */
$messages['tg-latn'] = array(
	'tmh-desc' => 'Ba dastgirandae baroi parvandahoi  Ogg Theora va Vorbis, bo paxşkunandai JavaScript',
	'tmh-short-audio' => 'Ogg $1 parvandai savtī, $2',
	'tmh-short-video' => 'Ogg $1 parvandai navorī, $2',
	'tmh-short-general' => 'Ogg $1 parvandai rasona, $2',
	'tmh-long-audio' => '(Ogg $1 parvandai savtī, tūl $2, $3)',
	'tmh-long-video' => '(Ogg $1 parvandai navorī, tūl $2, $4×$5 pikselho, $3)',
	'tmh-long-multiplexed' => "(Parvandai Ogg savtī/navorī pecida, $1, tūl $2, $4×$5 pikselho, dar maçmū' $3)",
	'tmh-long-general' => '(Parvandai rasonai Ogg, tūl $2, $3)',
	'tmh-long-error' => '(Parvandai ƣajrimiçozi ogg: $1)',
	'tmh-play' => 'Paxş',
	'tmh-pause' => 'Sukut',
	'tmh-stop' => "Qat'",
	'tmh-play-video' => 'Paxşi navor',
	'tmh-play-sound' => 'Paxşi ovoz',
	'tmh-no-player' => 'Bubaxşed, dastgohi şumo narmafzori paxşkunandai munosibe nadorad. Lutfan <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">jak barnomai paxşkunandaro borgirī kuned</a>.',
	'tmh-no-xiphqt' => 'Afzunai XiphQT baroi QuickTime ba nazar namerasad. QuickTime nametavonad biduni in afzuna parvandahoi tmh-ro paxş kunad. Lutfan <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT-ro borgirī kuned</a>  jo digar narmafzori paxşkunandaro intixob namoed.',
	'tmh-player-thumbnail' => 'Faqat aks hanūz',
	'tmh-player-soundthumb' => 'Paxşkunanda nest',
	'tmh-player-selected' => '(intixobşuda)',
	'tmh-use-player' => 'Istifoda az paxşkunanda:',
	'tmh-more' => 'Beştar...',
	'tmh-dismiss' => 'Bastan',
	'tmh-download' => 'Borgiriji parvanda',
	'tmh-desc-link' => 'Dar borai in parvanda',
);

/** Thai (ไทย)
 * @author Manop
 * @author Woraponboonkerd
 */
$messages['th'] = array(
	'tmh-play' => 'เล่น',
	'tmh-pause' => 'หยุดชั่วคราว',
	'tmh-stop' => 'หยุด',
	'tmh-play-video' => 'เล่นวิดีโอ',
	'tmh-play-sound' => 'เล่นเสียง',
	'tmh-no-player' => 'ขออภัย ระบบของคุณไม่มีซอฟต์แวร์ที่สนับสนุนไฟล์สื่อนี้
กรุณา<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ดาวน์โหลดซอฟต์แวร์เล่นสื่อ</a>',
	'tmh-no-xiphqt' => 'ไม่พบซอฟต์แวร์เสริม XiphQT ของโปรแกรม QuickTime บนระบบของคุณ
โปรแกรม QuickTime ไม่สามารถเล่นไฟล์สกุล Ogg ได้ถ้าไม่มีโปรแกรมเสริมนี้
กรุณา<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">ดวาน์โหลด XiphQT</a> หรือเลือกโปรแกรมอื่น',
);

/** Turkmen (Türkmençe)
 * @author Hanberke
 */
$messages['tk'] = array(
	'tmh-desc' => 'Ogg Theora we Vorbis faýllary üçin işleýji, JavaScript pleýeri bilen bilelikde',
	'tmh-short-audio' => 'Ogg $1 ses faýly, $2',
	'tmh-short-video' => 'Ogg $1 wideo faýly, $2',
	'tmh-short-general' => 'Ogg $1 media faýly, $2',
	'tmh-long-audio' => '(Ogg $1 ses faýly, uzynlyk $2, $3)',
	'tmh-long-video' => '(Ogg $1 wideo faýly, uzynlyk $2, $4×$5 piksel, $3)',
	'tmh-long-multiplexed' => '(Ogg multipleks audio/wideo faýly, $1, uzynlyk $2, $4×$5 piksel, $3 jemi)',
	'tmh-long-general' => '(Ogg media faýly, uzynlyk $2, $3)',
	'tmh-long-error' => '(Nädogry ogg faýly: $1)',
	'tmh-play' => 'Oýnat',
	'tmh-pause' => 'Pauza',
	'tmh-stop' => 'Duruz',
	'tmh-play-video' => 'Wideo oýnat',
	'tmh-play-sound' => 'Ses oýnat',
	'tmh-no-player' => 'Gynansak-da, ulgamyňyzda goldanylýan haýsydyr bir pleýer programmaňyz ýok ýaly-la.
<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download"> Pleýer düşüriň</a>.',
	'tmh-no-xiphqt' => 'QuickTime üçin XiphQT komponentiňiz ýok bolarly.
QuickTime bu komponent bolmasa Ogg faýllaryny oýnadyp bilmeýär.
<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT-i düşüriň</a> ýa-da başga bir pleýer saýlaň.',
	'tmh-player-videoElement' => 'Milli brauzer goldawy',
	'tmh-player-oggPlugin' => 'Brauzer goşmaça moduly',
	'tmh-player-thumbnail' => 'Diňe hereketsiz surat',
	'tmh-player-soundthumb' => 'Pleýer ýok',
	'tmh-player-selected' => '(saýlanylan)',
	'tmh-use-player' => 'Pleýer ulan:',
	'tmh-more' => 'Has köp...',
	'tmh-dismiss' => 'Ýap',
	'tmh-download' => 'Faýl düşür',
	'tmh-desc-link' => 'Bu faýl hakda',
);

/** Tagalog (Tagalog)
 * @author AnakngAraw
 */
$messages['tl'] = array(
	'tmh-desc' => 'Tagahawak para sa mga talaksang Ogg Theora at Vorbis, na may panugtog/pampaandar na JavaScript',
	'tmh-short-audio' => '$1 na talaksang pangtunog ng Ogg, $2',
	'tmh-short-video' => "$1 talaksang pampalabas (''video'') ng Ogg, $2",
	'tmh-short-general' => '$1 talaksang pangmidya ng Ogg, $2',
	'tmh-long-audio' => '($1 talaksang pantunog ng Ogg, haba $2, $3)',
	'tmh-long-video' => '($1 talaksan ng palabas ng Ogg, haba $2, $4×$5 mga piksel, $3)',
	'tmh-long-multiplexed' => '(magkasanib at nagsasabayang talaksang nadirinig o audio/palabas ng Ogg, $1, haba $2, $4×$5 mga piksel, $3 sa kalahatan)',
	'tmh-long-general' => "(Talaksang pangmidya ng ''Ogg'', haba $2, $3)",
	'tmh-long-error' => "(Hindi tanggap na talaksang ''ogg'': $1)",
	'tmh-play' => 'Paandarin',
	'tmh-pause' => 'Pansamantalang pahintuin',
	'tmh-stop' => 'Ihinto/itigil',
	'tmh-play-video' => "Paandarin ang palabas (''video'')",
	'tmh-play-sound' => 'Patugtugin ang tunog',
	'tmh-no-player' => 'Paumanhin, tila parang walang anumang sinusuportahang pamapatugtog/pampaandar na sopwer ang sistema mo.
<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Magkarga lamang po muna ng isang panugtog/pampaandar</a>.',
	'tmh-no-xiphqt' => 'Tila parang wala ka pang sangkap (komponente) na XiphQT para sa QuickTime.
Hindi makapagpapatugtog ang QuickTime ng mga talaksang Ogg kapag wala ang ganitong sangkap.
<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">Magkarga muna po ng XiphQT</a> o pumili ng iba pang panugtog/pampaandar.',
	'tmh-player-videoElement' => "Katutubong tagapagtangkilik/pangsuporta ng pantingin-tingin (''browser'')",
	'tmh-player-oggPlugin' => "Pampasak sa pantingin-tingin (''browser'')",
	'tmh-player-cortado' => 'Cortado (Java)',
	'tmh-player-vlc-mozilla' => 'VLC',
	'tmh-player-vlc-activex' => 'VLC (ActiveX)',
	'tmh-player-quicktime-mozilla' => 'QuickTime',
	'tmh-player-quicktime-activex' => 'QuickTime (ActiveX)',
	'tmh-player-totem' => 'Totem',
	'tmh-player-kmplayer' => 'KMPlayer',
	'tmh-player-kaffeine' => 'Kaffeine',
	'tmh-player-mplayerplug-in' => "pampasak na pampatugtog/pampaandar ng tunog (''mplayerplug-in'')",
	'tmh-player-thumbnail' => 'Larawang hindi gumagalaw lamang',
	'tmh-player-soundthumb' => 'Walang pampatugtog/pampaandar',
	'tmh-player-selected' => '(napili na)',
	'tmh-use-player' => 'Gamitin ang pampaandar:',
	'tmh-more' => 'Marami pa…',
	'tmh-dismiss' => 'Isara',
	'tmh-download' => 'Ikarga ang talaksan',
	'tmh-desc-link' => 'Tungkol sa talaksang ito',
);

/** Turkish (Türkçe)
 * @author Erkan Yilmaz
 * @author Joseph
 * @author Mach
 * @author Runningfridgesrule
 * @author Srhat
 */
$messages['tr'] = array(
	'tmh-desc' => 'Ogg Theora ve Vorbis dosyaları için işleyici, JavaScript oynatıcısı ile',
	'tmh-short-audio' => 'Ogg $1 ses dosyası, $2',
	'tmh-short-video' => 'Ogg $1 film dosyası, $2',
	'tmh-short-general' => 'Ogg $1 medya dosyası, $2',
	'tmh-long-audio' => '(Ogg $1 ses dosyası, süre $2, $3)',
	'tmh-long-video' => '(Ogg $1 film dosyası, süre $2, $4×$5 piksel, $3)',
	'tmh-long-multiplexed' => '(Ogg çok düzeyli ses/film dosyası, $1, süre $2, $4×$5 piksel, $3 genelde)',
	'tmh-long-general' => '(Ogg medya dosyası, süre $2, $3)',
	'tmh-long-error' => '(Geçersiz ogg dosyası: $1)',
	'tmh-play' => 'Oynat',
	'tmh-pause' => 'Duraklat',
	'tmh-stop' => 'Durdur',
	'tmh-play-video' => 'Video filmini oynat',
	'tmh-play-sound' => 'Sesi oynat',
	'tmh-no-player' => 'Üzgünüz, sisteminiz desteklenen herhangi bir oynatıcı yazılımına sahip gibi görünmüyor.
Lütfen <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">bir oynatıcı indirin</a>.',
	'tmh-no-xiphqt' => 'QuickTime için XiphQT bileşenine sahip değil görünüyorsunuz.
QuickTime bu bileşen olmadan Ogg dosyalarını oynatamaz.
Lütfen <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">XiphQT\'i indirin</a> ya da başka bir oynatıcı seçin.',
	'tmh-player-videoElement' => 'Yerel tarayıcı desteği',
	'tmh-player-oggPlugin' => 'Tarayıcı eklentisi',
	'tmh-player-thumbnail' => 'Henüz sadece resimdir',
	'tmh-player-soundthumb' => 'Oynatıcı yok',
	'tmh-player-selected' => '(seçilmiş)',
	'tmh-use-player' => 'Oynatıcıyı kullanın:',
	'tmh-more' => 'Daha...',
	'tmh-dismiss' => 'Kapat',
	'tmh-download' => 'Dosya indir',
	'tmh-desc-link' => 'Bu dosya hakkında',
);

/** Tsonga (Xitsonga)
 * @author Thuvack
 */
$messages['ts'] = array(
	'tmh-more' => 'Swinwana…',
	'tmh-dismiss' => 'Pfala',
);

/** Ukrainian (Українська)
 * @author AS
 * @author Ahonc
 * @author NickK
 * @author Prima klasy4na
 */
$messages['uk'] = array(
	'tmh-desc' => 'Оброблювач файлів Ogg Theora і Vorbis з використанням JavaScript-програвача',
	'tmh-short-audio' => 'Звуковий файл Ogg $1, $2',
	'tmh-short-video' => 'Відео-файл Ogg $1, $2',
	'tmh-short-general' => 'Файл Ogg $1, $2',
	'tmh-long-audio' => '(звуковий файл Ogg $1, довжина $2, $3)',
	'tmh-long-video' => '(відео-файл Ogg $1, довжина $2, $4×$5 пікселів, $3)',
	'tmh-long-multiplexed' => '(мультиплексний аудіо/відео-файл ogg, $1, довжина $2, $4×$5 пікселів, $3 усього)',
	'tmh-long-general' => '(медіа-файл Ogg, довжина $2, $3)',
	'tmh-long-error' => '(Неправильний tmh-файл: $1)',
	'tmh-play' => 'Відтворити',
	'tmh-pause' => 'Пауза',
	'tmh-stop' => 'Зупинити',
	'tmh-play-video' => 'Відтворити відео',
	'tmh-play-sound' => 'Відтворити звук',
	'tmh-no-player' => 'Вибачте, ваша ситема не має необхідного програмного забезпечення для відтворення файлів. Будь ласка, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">завантажте програвач</a>.',
	'tmh-no-xiphqt' => 'Відсутній компонент XiphQT для QuickTime.
QuickTime не може відтворювати tmh-файли без цього компонента.
Будь ласка, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">завантажте XiphQT</a> або оберіть інший програвач.',
	'tmh-player-videoElement' => 'Рідна підтримка веб-оглядача',
	'tmh-player-oggPlugin' => 'Плаґін для браузера',
	'tmh-player-thumbnail' => 'Тільки нерухоме зображення',
	'tmh-player-soundthumb' => 'Нема програвача',
	'tmh-player-selected' => '(обраний)',
	'tmh-use-player' => 'Використовувати програвач:',
	'tmh-more' => 'Більше…',
	'tmh-dismiss' => 'Закрити',
	'tmh-download' => 'Завантажити файл',
	'tmh-desc-link' => 'Інформація про цей файл',
);

/** Vèneto (Vèneto)
 * @author Candalua
 */
$messages['vec'] = array(
	'tmh-desc' => 'Gestor par i file Ogg Theora e Vorbis, con riprodutor JavaScript',
	'tmh-short-audio' => 'File audio Ogg $1, $2',
	'tmh-short-video' => 'File video Ogg $1, $2',
	'tmh-short-general' => 'File multimedial Ogg $1, $2',
	'tmh-long-audio' => '(File audio Ogg $1, durata $2, $3)',
	'tmh-long-video' => '(File video Ogg $1, durata $2, dimensioni $4×$5 pixel, $3)',
	'tmh-long-multiplexed' => '(File audio/video multiplexed Ogg $1, durata $2, dimensioni $4×$5 pixel, conplessivamente $3)',
	'tmh-long-general' => '(File multimedial Ogg, durata $2, $3)',
	'tmh-long-error' => '(File ogg mìa valido: $1)',
	'tmh-play' => 'Riprodusi',
	'tmh-pause' => 'Pausa',
	'tmh-stop' => 'Fèrma',
	'tmh-play-video' => 'Varda el video',
	'tmh-play-sound' => 'Scolta el file',
	'tmh-no-player' => 'Semo spiacenti, ma sul to sistema no risulta instalà nissun software de riproduzion conpatibile. Par piaser <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">scàrichete un letor</a> che vaga ben.',
	'tmh-no-xiphqt' => 'No risulta mìa instalà el conponente XiphQT de QuickTime. Senza sto conponente no se pode mìa riprodur i file Ogg con QuickTime. Par piaser, <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">scàrichete XiphQT</a> o siegli n\'altro letor.',
	'tmh-player-videoElement' => 'Suporto browser zà de suo (nativo)',
	'tmh-player-oggPlugin' => 'Plugin browser',
	'tmh-player-thumbnail' => 'Solo imagini fisse',
	'tmh-player-soundthumb' => 'Nissun letor',
	'tmh-player-selected' => '(selezionà)',
	'tmh-use-player' => 'Dòpara el letor:',
	'tmh-more' => 'Altro...',
	'tmh-dismiss' => 'Sara',
	'tmh-download' => 'Descarga el file',
	'tmh-desc-link' => 'Informazion su sto file',
);

/** Veps (Vepsan kel')
 * @author Игорь Бродский
 */
$messages['vep'] = array(
	'tmh-play' => 'Väta',
	'tmh-pause' => 'Pauz',
	'tmh-stop' => 'Azotada',
	'tmh-play-video' => 'Ozutada video',
	'tmh-play-sound' => 'Väta kulundad',
	'tmh-player-oggPlugin' => 'Kaclim-plagin',
	'tmh-player-soundthumb' => 'Ei ole plejerad',
	'tmh-player-selected' => '(valitud)',
	'tmh-use-player' => 'Kävutada plejer:',
	'tmh-more' => 'Enamba...',
	'tmh-dismiss' => 'Peitta',
	'tmh-download' => 'Jügutoitta fail',
	'tmh-desc-link' => 'Informacii neciš failas',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 * @author Vinhtantran
 */
$messages['vi'] = array(
	'tmh-desc' => 'Bộ trình bày các tập tin Ogg Theora và Vorbis dùng hộp chơi phương tiện bằng JavaScript',
	'tmh-short-audio' => 'Tập tin âm thanh Ogg $1, $2',
	'tmh-short-video' => 'Tập tin video Ogg $1, $2',
	'tmh-short-general' => 'Tập tin Ogg $1, $2',
	'tmh-long-audio' => '(tập tin âm thanh Ogg $1, dài $2, $3)',
	'tmh-long-video' => '(tập tin video Ogg $1, dài $2, $4×$5 điểm ảnh, $3)',
	'tmh-long-multiplexed' => '(tập tin Ogg có âm thanh và video ghép kênh, $1, dài $2, $4×$5 điểm ảnh, $3 tất cả)',
	'tmh-long-general' => '(tập tin phương tiện Ogg, dài $2, $3)',
	'tmh-long-error' => '(Tập tin Ogg có lỗi: $1)',
	'tmh-play' => 'Chơi',
	'tmh-pause' => 'Tạm ngừng',
	'tmh-stop' => 'Ngừng',
	'tmh-play-video' => 'Coi video',
	'tmh-play-sound' => 'Nghe âm thanh',
	'tmh-no-player' => 'Rất tiếc, hình như máy tính của bạn cần thêm phần mềm. Xin <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/vi">tải xuống chương trình chơi nhạc</a>.',
	'tmh-no-xiphqt' => 'Hình như bạn không có bộ phận XiphQT cho QuickTime, nên QuickTime không thể chơi những tập tin Ogg được. Xin <a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download/vi">truyền xuống XiphQT</a> hay chọn một chương trình chơi nhạc khác.',
	'tmh-player-videoElement' => 'Bộ chơi có sẵn trong trình duyệt',
	'tmh-player-oggPlugin' => 'Phần bổ trợ trình duyệt',
	'tmh-player-thumbnail' => 'Chỉ hiển thị hình tĩnh',
	'tmh-player-soundthumb' => 'Tắt',
	'tmh-player-selected' => '(được chọn)',
	'tmh-use-player' => 'Chọn chương trình chơi:',
	'tmh-more' => 'Thêm nữa…',
	'tmh-dismiss' => 'Đóng',
	'tmh-download' => 'Tải tập tin xuống',
	'tmh-desc-link' => 'Chi tiết của tập tin này',
);

/** Volapük (Volapük)
 * @author Malafaya
 * @author Smeira
 */
$messages['vo'] = array(
	'tmh-player-videoElement' => 'Stüt bevüresodanaföm gebidon',
	'tmh-more' => 'Pluikos...',
	'tmh-dismiss' => 'Färmükön',
	'tmh-download' => 'Donükön ragivi',
	'tmh-desc-link' => 'Tefü ragiv at',
);

/** Walloon (Walon) */
$messages['wa'] = array(
	'tmh-dismiss' => 'Clôre',
);

/** Cantonese (粵語) */
$messages['yue'] = array(
	'tmh-desc' => 'Ogg Theora 同 Vorbis 檔案嘅處理器，加埋 JavaScript 播放器',
	'tmh-short-audio' => 'Ogg $1 聲檔，$2',
	'tmh-short-video' => 'Ogg $1 畫檔，$2',
	'tmh-short-general' => 'Ogg $1 媒檔，$2',
	'tmh-long-audio' => '(Ogg $1 聲檔，長度$2，$3)',
	'tmh-long-video' => '(Ogg $1 畫檔，長度$2，$4×$5像素，$3)',
	'tmh-long-multiplexed' => '(Ogg 多工聲／畫檔，$1，長度$2，$4×$5像素，總共$3)',
	'tmh-long-general' => '(Ogg 媒檔，長度$2，$3)',
	'tmh-long-error' => '(無效嘅ogg檔: $1)',
	'tmh-play' => '去',
	'tmh-pause' => '暫停',
	'tmh-stop' => '停',
	'tmh-play-video' => '去畫',
	'tmh-play-sound' => '去聲',
	'tmh-no-player' => '對唔住，你嘅系統並無任何可以支援得到嘅播放器。請安裝<a href="http://www.java.com/zh_TW/download/manual.jsp">Java</a>。',
	'tmh-no-xiphqt' => '你似乎無畀QuickTime用嘅XiphQT組件。響未有呢個組件嗰陣，QuickTime係唔可以播放Ogg檔案。請<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">下載XiphQT</a>或者揀過另外一個播放器。',
	'tmh-player-videoElement' => '<video>元素',
	'tmh-player-oggPlugin' => 'Ogg插件',
	'tmh-player-thumbnail' => '只有靜止圖像',
	'tmh-player-soundthumb' => '無播放器',
	'tmh-player-selected' => '(揀咗)',
	'tmh-use-player' => '使用播放器:',
	'tmh-more' => '更多...',
	'tmh-dismiss' => '閂',
	'tmh-download' => '下載檔案',
	'tmh-desc-link' => '關於呢個檔案',
);

/** Simplified Chinese (‪中文(简体)‬)
 * @author Gaoxuewei
 */
$messages['zh-hans'] = array(
	'tmh-desc' => 'Ogg Theora 和 Vorbis 文件的处理器，含 JavaScript 播放器',
	'tmh-short-audio' => 'Ogg $1 声音文件，$2',
	'tmh-short-video' => 'Ogg $1 视频文件，$2',
	'tmh-short-general' => 'Ogg $1 媒体文件，$2',
	'tmh-long-audio' => '（Ogg $1 声音文件，长度$2，$3）',
	'tmh-long-video' => '（Ogg $1 视频文件，长度$2，$4×$5像素，$3）',
	'tmh-long-multiplexed' => '（Ogg 多工声音／视频文件，$1，长度$2，$4×$5像素，共$3）',
	'tmh-long-general' => '（Ogg 媒体文件，长度$2，$3）',
	'tmh-long-error' => '（无效的ogg文件: $1）',
	'tmh-play' => '播放',
	'tmh-pause' => '暂停',
	'tmh-stop' => '停止',
	'tmh-play-video' => '播放视频',
	'tmh-play-sound' => '播放声音',
	'tmh-no-player' => '抱歉，您的系统并无任何可以支持播放的播放器。请安装<a href="http://www.java.com/zh_CN/download/manual.jsp">Java</a>。',
	'tmh-no-xiphqt' => '您似乎没有给QuickTime用的XiphQT组件。在未有这个组件的情况下，QuickTime是不能播放Ogg文件的。请<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">下载XiphQT</a>或者选取另一个播放器。',
	'tmh-player-videoElement' => '<video>元素',
	'tmh-player-oggPlugin' => 'Ogg插件',
	'tmh-player-thumbnail' => '只有静止图像',
	'tmh-player-soundthumb' => '沒有播放器',
	'tmh-player-selected' => '（已选取）',
	'tmh-use-player' => '使用播放器:',
	'tmh-more' => '更多...',
	'tmh-dismiss' => '关闭',
	'tmh-download' => '下载文件',
	'tmh-desc-link' => '关于这个文件',
);

/** Traditional Chinese (‪中文(繁體)‬)
 * @author Gaoxuewei
 * @author Mark85296341
 */
$messages['zh-hant'] = array(
	'tmh-desc' => 'Ogg Theora 和 Vorbis 檔案的處理器，含 JavaScript 播放器',
	'tmh-short-audio' => 'Ogg $1 聲音檔案，$2',
	'tmh-short-video' => 'Ogg $1 影片檔案，$2',
	'tmh-short-general' => 'Ogg $1 媒體檔案，$2',
	'tmh-long-audio' => '（Ogg $1 聲音檔案，長度$2，$3）',
	'tmh-long-video' => '（Ogg $1 影片檔案，長度$2，$4×$5像素，$3）',
	'tmh-long-multiplexed' => '（Ogg 多工聲音／影片檔案，$1，長度$2，$4×$5像素，共$3）',
	'tmh-long-general' => '（Ogg 媒體檔案，長度$2，$3）',
	'tmh-long-error' => '（無效的ogg檔案: $1）',
	'tmh-play' => '播放',
	'tmh-pause' => '暫停',
	'tmh-stop' => '停止',
	'tmh-play-video' => '播放影片',
	'tmh-play-sound' => '播放聲音',
	'tmh-no-player' => '抱歉，您的系統並無任何可以支援播放的播放器。請安裝<a href="http://www.java.com/zh_TW/download/manual.jsp">Java</a>。',
	'tmh-no-xiphqt' => '您似乎沒有給QuickTime用的XiphQT組件。在未有這個組件的情況下，QuickTime是不能播放Ogg檔案的。請<a href="http://www.mediawiki.org/wiki/Extension:OggHandler/Client_download">下載XiphQT</a>或者選取另一個播放器。',
	'tmh-player-videoElement' => '<video>元素',
	'tmh-player-oggPlugin' => 'Ogg插件',
	'tmh-player-thumbnail' => '只有靜止圖片',
	'tmh-player-soundthumb' => '沒有播放器',
	'tmh-player-selected' => '（已選取）',
	'tmh-use-player' => '使用播放器:',
	'tmh-more' => '更多...',
	'tmh-dismiss' => '關閉',
	'tmh-download' => '下載檔案',
	'tmh-desc-link' => '關於這個檔案',
);

