<?php
/**
 * Created by PhpStorm.
 * User: cagataycali
 * Date: 02/04/15
 * Time: 20:00
 */

#namespace \Controller;

#use entity

class AraclarController extends Controller
{

    #Öğretmen transferi
    protected $ogretmen_tc = "";
    protected $ogretmen_ad = "";
    protected $ogretmen_soyad = "";
    protected $ogretmen_dogum_yeri = "";
    protected $ogretmen_dogum_tarihi = "";
    protected $ogretmen_cinsiyet = "";
    protected $ogretmen_mezuniyet = "";
    protected $ogretmen_brans = "";
    protected $ogretmen_meslek = "";
    protected $ogretmen_mail = "";
    #Öğretmen transferi

    #Sınıf transferi
    protected $sinif_adi = "";
    #Sınıf transferi

    #Öğrenci transferi
    protected $ogrenci_tc = "";
    protected $ogrenci_ad = "";
    protected $ogrenci_soyad = "";
    protected $ogrenci_dogum_yeri = "";
    protected $ogrenci_dogum_tarihi = "";
    protected $ogrenci_cinsiyet = "";
    protected $ogrenci_anne_ad = "";
    protected $ogrenci_baba_ad = "";
    protected $ogrenci_veli_mail = "";
    protected $ogrenci_veli_cep = "";
    protected $ogrenci_sinif = "";
    #Öğrenci transferi

    #Yemek transferi
    protected $yemek_tarih  = "";
    protected $sabah  = "";
    protected $ogle  = "";
    protected $ikindi  = "";
    #Yemek transferi

    #Veli transferi
    protected $veli_tc = "";
    protected $veli_ad = "";
    protected $veli_soyad = "";
    protected $veli_mail = "";
    protected $veli_cep = "";
    protected $veli_dogum_yeri = "";
    protected $veli_dogum_tarihi = "";
    protected $veli_cinsiyeti = "";
    protected $veli_yakinlik = "";
    protected $veli_mezuniyet = "";
    protected $veli_meslek = "";
    protected $veli_ogrenci_tc = "";
    #Veli transferi

    /**
     * Anasayfa
     */
    public function indexAction()
    {
        return $this->render('ClientAssistantBundle:Araclar:index.html.twig');
    }

    /**
     * Transfer
     */
    public function transferAction()
    {
        return $this->render('ClientAssistantBundle:Araclar:transfer.html.twig');
    }

    /**
     * Excel okumak için
     */
    public function excelAction(Request $request)
    {
        /**
         * Kimlik
         */
        $kimlik = $this->kimlik();

        /**
         * Okul
         */
        $okul = $kimlik->getOkul();

        /**
         * Gelen dosya tipini bulalım
         */
        $tip = $request->request->get('tip');

        /**
         * Dosyayı upload edelim
         */
        $upload_dir = "../web/assets/upload";
        $upload_path = $upload_dir."/";

        /**
         * Uzantıyı bölmek için verileri alalım
         */
        $dosya = $_FILES["dosya"]["name"]; # Tam ismi veriyor
        $dosya_tmp = $_FILES["dosya"]["tmp_name"]; # Tam konumunu ( yerel ) veriyor

        # todo : Uzantı kontrolü yapılacak

        /**
         * İsme hash değeri ekleyelim
         */
        $time = time();

        $random = rand(1,10000); # Random sayı tutalım

        $dosya_ismi = $time.$random; # İsmi değiştirelim

        /**
         * Uzantıları ayıralım.
         */
        $parcalar = explode('.', $dosya);
        $uzanti = end($parcalar);

        /**
         * Dosyanını yolunu belirleyelim.
         */
        $dosya_yolu = $upload_path . $dosya_ismi . "." .$uzanti ;

        /**
         * Dosyayı yükleyelim.
         */
        move_uploaded_file($dosya_tmp , $dosya_yolu);

        switch ($tip)
        {
            case "ogretmen":

                $this->ogretmenTransferAction($dosya_yolu , $okul );

                break;
            case "sinif":

                $this->sinifTransferAction($dosya_yolu , $okul );

                break;
            case "ogrenci":

                $this->ogrenciTransferAction($dosya_yolu , $okul );

                break;
            case "yemek_menusu":

                $this->yemekMenusuTransferAction($dosya_yolu , $okul );

                break;
            case "veli":

                $this->veliTransferAction($dosya_yolu , $okul );

                break;
            # ..
            default:
                echo "Başarısız..";
        }

        unlink($dosya_yolu);

        return $this->render('@ClientAssistant/Araclar/transfer.html.twig');

    }

    public function ogretmenTransferAction($dosya_yolu , $okul )
    {
        /**
         * Doctrine
         */
        $em = $this->getDoctrine()->getManager();

        $objPHPExcel = new \PHPExcel();

        //  $objReader = new \PHPExcel_Reader_Excel5();
        $objReader = new \PHPExcel_Reader_Excel2007();

        $objReader->setReadDataOnly(true);

        $objPHPExcel = $objReader->load($dosya_yolu);

        // ITERATOR
        $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator(); // "activeSheet" seçili olduğu zaman boş olan diğer sayfaları başlangıç olarak kabul edebiliyor.
//        $rowIterator = $objPHPExcel->getSheetByName('Sheet1')->getRowIterator();


        // PARSING
        $sheet = $objPHPExcel->getActiveSheet();

        foreach ($rowIterator as $row) {//5.satırdan itibaren başlıyor
            if (
                $row->getRowIndex() > 4 &&
                $sheet->getCell('B' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('C' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('D' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('E' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('F' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('G' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('H' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('I' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('J' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('K' . $row->getRowIndex())->getCalculatedValue() != ""
            ) {

                $this->ogretmen_tc = $sheet->getCell('B' . $row->getRowIndex())->getCalculatedValue();
                $this->ogretmen_ad = $sheet->getCell('C' . $row->getRowIndex())->getCalculatedValue();
                $this->ogretmen_soyad = $sheet->getCell('D' . $row->getRowIndex())->getCalculatedValue();
                $this->ogretmen_mail = $sheet->getCell('E' . $row->getRowIndex())->getCalculatedValue();
                $this->ogretmen_dogum_yeri = $sheet->getCell('F' . $row->getRowIndex())->getCalculatedValue();
                $this->ogretmen_dogum_tarihi = date("d-m-Y", \PHPExcel_Shared_Date::ExcelToPHP($sheet->getCell('G' . $row->getRowIndex())->getCalculatedValue()));
                $this->ogretmen_cinsiyet = $sheet->getCell('H' . $row->getRowIndex())->getCalculatedValue();
                $this->ogretmen_mezuniyet = $sheet->getCell('I' . $row->getRowIndex())->getCalculatedValue();
                $this->ogretmen_brans = $sheet->getCell('J' . $row->getRowIndex())->getCalculatedValue();
                $this->ogretmen_meslek = $sheet->getCell('K' . $row->getRowIndex())->getCalculatedValue();


                # Kullanıcı kaydını yapalım
                /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                $userManager = $this->get('fos_user.user_manager');

                $user = $userManager->createUser();
                $user->setEnabled(true);
                $user->setEmail($this->ogretmen_mail);
                $user->setUsername($this->ogretmen_mail);
                $user->setPlainPassword($this->ogretmen_tc);
                $user->setRoles(array('ROLE_OGRETMEN'));

                $userManager->updateUser($user);
                # Kullanıcı kaydını yapalım


                #Kullanici ' ya okul ekleme
                $kullanici = $em->getRepository('CoreCommonBundle:Kullanici')->find($user);

                $kullanici->setOkul($okul);

                $em->persist($kullanici);
                #Kullanici ' ya okul ekleme

                /**
                 * Öğretmen kaydı
                 */
                $yeni_ogretmen = new Profil();

                /**
                 * Verileri içe aktaralım.
                 */
                $yeni_ogretmen->setTc($this->ogretmen_tc);
                $yeni_ogretmen->setAd($this->ogretmen_ad);
                $yeni_ogretmen->setSoyad($this->ogretmen_soyad);
                $yeni_ogretmen->setDogumYeri($this->ogretmen_dogum_yeri);
                $yeni_ogretmen->setDogumTarihi(new \DateTime($this->ogretmen_dogum_tarihi));
                $yeni_ogretmen->setCinsiyet($this->ogretmen_cinsiyet);
                $yeni_ogretmen->setMezuniyet($this->ogretmen_mezuniyet);
                $yeni_ogretmen->setBrans($this->ogretmen_brans);
                $yeni_ogretmen->setMeslek($this->ogretmen_meslek);
                $yeni_ogretmen->setOgretmen(1);

                /**
                 * İlişkili nesneler.
                 */
                $yeni_ogretmen->setKullanici($user); # Kullanici
                $yeni_ogretmen->setOkul($okul); # Okul

                /**
                 * Verileri satıra işleme işlemini tamamlayalım.
                 */
                $em->persist($yeni_ogretmen);

                # todo:  Bilgilendime E-Mail'i gönderilecek.


                /**
                 * Verileri tekrardan sıfırlayalım.
                 */
                $this->ogretmen_tc = "";
                $this->ogretmen_ad = "";
                $this->ogretmen_soyad = "";
                $this->ogretmen_dogum_yeri = "";
                $this->ogretmen_dogum_tarihi = "";
                $this->ogretmen_cinsiyet = "";
                $this->ogretmen_mezuniyet = "";
                $this->ogretmen_brans = "";
                $this->ogretmen_meslek = "";
            }

            /**
             * Kaydı tamamlayalım.
             */
            $em->flush();

        }
        // Flash Bag Mesajı
        $this->get('session')->getFlashBag()->set(
            'info',
            'Öğretmen listesi başarıyla içe aktarıldı!'
        );
    }

    public function sinifTransferAction($dosya_yolu , $okul)
    {
        /**
         * Doctrine
         */
        $em = $this->getDoctrine()->getManager();

        $objPHPExcel = new \PHPExcel();

        //  $objReader = new \PHPExcel_Reader_Excel5();
        $objReader = new \PHPExcel_Reader_Excel2007();

        $objReader->setReadDataOnly(true);

        $objPHPExcel = $objReader->load($dosya_yolu);

        // ITERATOR
        $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator(); // "activeSheet" seçili olduğu zaman boş olan diğer sayfaları başlangıç olarak kabul edebiliyor.
//        $rowIterator = $objPHPExcel->getSheetByName('Sheet1')->getRowIterator();


        // PARSING
        $sheet = $objPHPExcel->getActiveSheet();

        foreach ($rowIterator as $row) {//5.satırdan itibaren başlıyor
            if (
                $row->getRowIndex() > 4 &&
                $sheet->getCell('B' . $row->getRowIndex())->getCalculatedValue() != ""
            ) {

                $this->sinif_adi = $sheet->getCell('B' . $row->getRowIndex())->getCalculatedValue();

                /**
                 * Sınıf kaydı
                 */
                $yeni_sinif = new Sinif();

                $yeni_sinif->setBaslik($this->sinif_adi);

                /**
                 * İlişkili nesneler.
                 */
                $yeni_sinif->setOkul($okul); # Okul

                /**
                 * Verileri satıra işleme işlemini tamamlayalım.
                 */
                $em->persist($yeni_sinif);


                /**
                 * Verileri tekrardan sıfırlayalım.
                 */
                $this->sinif_adi = "";
            }
            /**
             * Kaydı tamamlayalım.
             */
            $em->flush();
        }
        // Flash Bag Mesajı
        $this->get('session')->getFlashBag()->set(
            'info',
            'Sınıf listesi başarıyla içe aktarıldı!'
        );
    }

    public function ogrenciTransferAction($dosya_yolu , $okul)
    {
        /**
         * Doctrine
         */
        $em = $this->getDoctrine()->getManager();

        $objPHPExcel = new \PHPExcel();

        //  $objReader = new \PHPExcel_Reader_Excel5();
        $objReader = new \PHPExcel_Reader_Excel2007();

        $objReader->setReadDataOnly(true);

        $objPHPExcel = $objReader->load($dosya_yolu);

        // ITERATOR
        $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator(); // "activeSheet" seçili olduğu zaman boş olan diğer sayfaları başlangıç olarak kabul edebiliyor.
//        $rowIterator = $objPHPExcel->getSheetByName('Sheet1')->getRowIterator();


        // PARSING
        $sheet = $objPHPExcel->getActiveSheet();

        foreach ($rowIterator as $row) {//5.satırdan itibaren başlıyor
            if (
                $row->getRowIndex() > 4 &&
                $sheet->getCell('B' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('C' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('D' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('E' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('F' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('G' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('H' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('I' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('J' . $row->getRowIndex())->getCalculatedValue() != ""
            ) {

                $this->ogrenci_tc = $sheet->getCell('B' . $row->getRowIndex())->getCalculatedValue();
                $this->ogrenci_ad = $sheet->getCell('C' . $row->getRowIndex())->getCalculatedValue();
                $this->ogrenci_soyad = $sheet->getCell('D' . $row->getRowIndex())->getCalculatedValue();
                $this->ogrenci_dogum_yeri = $sheet->getCell('E' . $row->getRowIndex())->getCalculatedValue();
                $this->ogrenci_dogum_tarihi = date("d-m-Y", \PHPExcel_Shared_Date::ExcelToPHP($sheet->getCell('F' . $row->getRowIndex())->getCalculatedValue()));
                $this->ogrenci_cinsiyet = $sheet->getCell('G' . $row->getRowIndex())->getCalculatedValue();
                $this->ogrenci_anne_ad = $sheet->getCell('H' . $row->getRowIndex())->getCalculatedValue();
                $this->ogrenci_baba_ad = $sheet->getCell('I' . $row->getRowIndex())->getCalculatedValue();
                $this->ogrenci_veli_mail = $sheet->getCell('J' . $row->getRowIndex())->getCalculatedValue();
                $this->ogrenci_veli_cep = $sheet->getCell('K' . $row->getRowIndex())->getCalculatedValue();
                $this->ogrenci_sinif = $sheet->getCell('L' . $row->getRowIndex())->getCalculatedValue();

                /**
                 * Sınıf sorgusu yapalım.
                 */
                $sinif = $em ->getRepository( 'CoreCommonBundle:Sinif' ) -> findOneBy(array('baslik'=>$this->ogrenci_sinif));

                if(!$sinif)
                {

                    /**
                     * Sınıf kaydı
                     */
                    $yeni_sinif = new Sinif();

                    $yeni_sinif->setBaslik($this->ogrenci_sinif);

                    /**
                     * İlişkili nesneler.
                     */
                    $yeni_sinif->setOkul($okul); # Okul

                    /**
                     * Verileri satıra işleme işlemini tamamlayalım.
                     */
                    $em->persist($yeni_sinif);

                }

                /**
                 * Öğrenci kaydını yapalım
                 */
                $yeni_ogrenci = new Ogrenci();

                /**
                 * Verileri işleyelim
                 */
                $yeni_ogrenci -> setOkul($okul);
                $yeni_ogrenci -> setAd($this->ogrenci_ad);
                $yeni_ogrenci -> setAnneAdi($this->ogrenci_anne_ad);
                $yeni_ogrenci -> setBabaAdi($this->ogrenci_baba_ad);
                $yeni_ogrenci -> setCinsiyet($this->ogrenci_cinsiyet);
                $yeni_ogrenci -> setDogumTarihi(new \DateTime($this->ogrenci_dogum_tarihi));
                $yeni_ogrenci -> setSoyad($this->ogrenci_soyad);
                $yeni_ogrenci -> setSinif($sinif);
                $yeni_ogrenci -> setTc($this->ogrenci_tc);
                $yeni_ogrenci -> setDogumYeri($this->ogrenci_dogum_yeri);

                /**
                 * Yeni öğrenci satırını tamamlayalım.
                 */
                $em ->persist($yeni_ogrenci);

                /**
                 * Velinin mail adresi girildiyse..
                 */
                if($this->ogrenci_veli_mail)
                {
                    # Kullanıcı kaydını yapalım
                    /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                    $userManager = $this->get('fos_user.user_manager');

                    $user = $userManager->createUser();
                    $user->setEnabled(true);
                    $user->setEmail($this->ogrenci_veli_mail);
                    $user->setUsername($this->ogrenci_veli_mail);

                    /**
                     * Velinin cep telefon bilgisi verildiyse.
                     */
                    if ( !$this->ogrenci_veli_cep )
                    {
                        $user->setPlainPassword(rand(1,9999)."velibis");
                    }
                    else
                    {
                        $user->setPlainPassword($this->ogrenci_veli_cep);
                    }

                    $userManager->updateUser($user);

                    /**
                     * Velinin profili
                     */
                    $veli_profil = new Profil();
                    $veli_profil->setDogumYeri("");
                    $veli_profil->setTc("");
                    $veli_profil->setSoyad("");
                    $veli_profil->setAd($this->ogrenci_anne_ad);
                    $veli_profil->setKullanici($user);
                    $veli_profil->setVeli(1);
                    $veli_profil->setOkul($okul);

                    $em->persist($veli_profil);


                    /**
                     * Veliye öğrenci ilişkisi sağlayalım
                     */
                    $veli_ogrenci = $em -> getRepository( 'CoreCommonBundle:OgrenciVeli' ) -> findOneBy(array('profil'=>$veli_profil,'ogrenci'=>$yeni_ogrenci));

                    /**
                     * Eğer yoksa..
                     */
                    if(!$veli_ogrenci)
                    {
                        $veli_ogrenci = new OgrenciVeli();
                        $veli_ogrenci->setOgrenci($yeni_ogrenci);
                        $veli_ogrenci->setProfil($veli_profil);

                        $em->persist($veli_ogrenci);
                    }

                    $user->setRoles(array('ROLE_VELI'));

                    # Kullanıcı kaydını yapalım

                    #Kullanici ' ya okul ekleme
                    $kullanici = $em->getRepository('CoreCommonBundle:Kullanici')->find($user);

                    $kullanici->setOkul($okul);

                    $em->persist($kullanici);
                    #Kullanici ' ya okul ekleme


                    $this->ogrenci_tc = "";
                    $this->ogrenci_ad = "";
                    $this->ogrenci_soyad = "";
                    $this->ogrenci_dogum_tarihi = "";
                    $this->ogrenci_dogum_yeri = "";
                    $this->ogrenci_anne_ad = "";
                    $this->ogrenci_baba_ad = "";
                    $this->ogrenci_veli_cep = "";
                    $this->ogrenci_veli_mail = "";
                    $this->ogrenci_cinsiyet = "";
                    $this->ogrenci_sinif = "";

                }

            }
            /**
             * Kaydı tamamlayalım.
             */
            $em->flush();
        }


        // Flash Bag Mesajı
        $this->get('session')->getFlashBag()->set(
            'info',
            'Öğrenci listesi başarıyla içe aktarıldı!'
        );
    }

    public function yemekMenusuTransferAction($dosya_yolu , $okul)
    {
        /**
         * Doctrine
         */
        $em = $this->getDoctrine()->getManager();

        $objPHPExcel = new \PHPExcel();

        //  $objReader = new \PHPExcel_Reader_Excel5();
        $objReader = new \PHPExcel_Reader_Excel2007();

        $objReader->setReadDataOnly(true);

        $objPHPExcel = $objReader->load($dosya_yolu);

        // ITERATOR
        $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator(); // "activeSheet" seçili olduğu zaman boş olan diğer sayfaları başlangıç olarak kabul edebiliyor.
//        $rowIterator = $objPHPExcel->getSheetByName('Sheet1')->getRowIterator();


        // PARSING
        $sheet = $objPHPExcel->getActiveSheet();

        foreach ($rowIterator as $row) {//5.satırdan itibaren başlıyor
            if (
                $row->getRowIndex() > 4 &&
                $sheet->getCell('B' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('C' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('D' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('E' . $row->getRowIndex())->getCalculatedValue() != ""
            ) {

                $this->yemek_tarih = date("d-m-Y", \PHPExcel_Shared_Date::ExcelToPHP($sheet->getCell('B' . $row->getRowIndex())->getCalculatedValue()));
                $this->sabah = $sheet->getCell('C' . $row->getRowIndex())->getCalculatedValue();
                $this->ogle = $sheet->getCell('D' . $row->getRowIndex())->getCalculatedValue();
                $this->ikindi = $sheet->getCell('E' . $row->getRowIndex())->getCalculatedValue();

                /**
                 * Sınıf kaydı
                 */
                $yemek_menusu = new YemekMenusu();

                $yemek_menusu->setSabah($this->sabah);
                $yemek_menusu->setOgle($this->ogle);
                $yemek_menusu->setIkindi($this->ikindi);

                /**
                 * İlişkili nesneler.
                 */
                $yemek_menusu->setOkul($okul); # Okul

                /**
                 * Verileri satıra işleme işlemini tamamlayalım.
                 */
                $em->persist($yemek_menusu);

                /**
                 * Verileri tekrardan sıfırlayalım.
                 */
                $this->yemek_tarih = "";
                $this->sabah = "";
                $this->ogle = "";
                $this->ikindi = "";
            }
            /**
             * Kaydı tamamlayalım.
             */
            $em->flush();
        }
        // Flash Bag Mesajı
        $this->get('session')->getFlashBag()->set(
            'info',
            'Yemek listesi başarıyla içe aktarıldı!'
        );

    }

    public function veliTransferAction($dosya_yolu , $okul)
    {
        /**
         * Doctrine
         */
        $em = $this->getDoctrine()->getManager();

        $objPHPExcel = new \PHPExcel();

        //  $objReader = new \PHPExcel_Reader_Excel5();
        $objReader = new \PHPExcel_Reader_Excel2007();

        $objReader->setReadDataOnly(true);

        $objPHPExcel = $objReader->load($dosya_yolu);

        // ITERATOR
        $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator(); // "activeSheet" seçili olduğu zaman boş olan diğer sayfaları başlangıç olarak kabul edebiliyor.
//        $rowIterator = $objPHPExcel->getSheetByName('Sheet1')->getRowIterator();


        // PARSING
        $sheet = $objPHPExcel->getActiveSheet();

        foreach ($rowIterator as $row) {//5.satırdan itibaren başlıyor
            if (
                $row->getRowIndex() > 4 &&
                $sheet->getCell('B' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('C' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('D' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('E' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('F' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('G' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('H' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('I' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('J' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('K' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('L' . $row->getRowIndex())->getCalculatedValue() != "" &&
                $sheet->getCell('M' . $row->getRowIndex())->getCalculatedValue() != ""
            )
            {

                $this->veli_tc = $sheet->getCell('B' . $row->getRowIndex())->getCalculatedValue();
                $this->veli_ad = $sheet->getCell('C' . $row->getRowIndex())->getCalculatedValue();
                $this->veli_soyad = $sheet->getCell('D' . $row->getRowIndex())->getCalculatedValue();
                $this->veli_mail = $sheet->getCell('E' . $row->getRowIndex())->getCalculatedValue();
                $this->veli_cep = $sheet->getCell('F' . $row->getRowIndex())->getCalculatedValue();
                $this->veli_dogum_yeri = $sheet->getCell('G' . $row->getRowIndex())->getCalculatedValue();
                $this->veli_dogum_tarihi = date("d-m-Y", \PHPExcel_Shared_Date::ExcelToPHP($sheet->getCell('H' . $row->getRowIndex())->getCalculatedValue()));
                $this->veli_cinsiyeti = $sheet->getCell('I' . $row->getRowIndex())->getCalculatedValue();
                $this->veli_yakinlik = $sheet->getCell('J' . $row->getRowIndex())->getCalculatedValue();
                $this->veli_mezuniyet = $sheet->getCell('K' . $row->getRowIndex())->getCalculatedValue();
                $this->veli_meslek = $sheet->getCell('L' . $row->getRowIndex())->getCalculatedValue();
                $this->veli_ogrenci_tc = $sheet->getCell('M' . $row->getRowIndex())->getCalculatedValue();

                /**
                 * Veli Profil Kaydı Yapalım
                 */
                if($this->veli_mail)
                {
                    # Kullanıcı kaydını yapalım
                    /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                    $userManager = $this->get('fos_user.user_manager');

                    /**
                     * Veli mail adresini kayıt ettiysek kaydı bitireceğiz
                     */
                    $user = $this->get('fos_user.user_manager')->findUserByEmail($this->veli_mail);

                    /**
                     * Veli kullanıcısı yoksa!
                     */
                    if(!$user)
                    {

                        /**
                         * Kullanıcı oluşturmayı başlatalım.
                         */
                        $user = $userManager->createUser();
                        $user->setEnabled(true); # Aktif edelim
                        $user->setEmail($this->veli_mail);  # VELi mail adresini işleyelim
                        $user->setUsername($this->veli_mail); # Veli kullanıcı adınıda mail adresi olarak girelim

                        /**
                         * Velinin cep telefon bilgisi verildiyse.
                         */
                        if ( !$this->veli_cep )
                        {
                            $user->setPlainPassword(rand(1,9999)."velibis"); #  Veli şifresini girelim.
                        }
                        else
                        {
                            $user->setPlainPassword($this->veli_cep);
                        }

                        $userManager->updateUser($user);

                    }

                    /**
                     * Velinin profili
                     */
                    $veli_profil = new Profil();
                    $veli_profil->setDogumYeri($this->veli_dogum_yeri);
                    $veli_profil->setDogumTarihi(new \DateTime($this->veli_dogum_tarihi));
                    $veli_profil->setTc($this->veli_tc);
                    $veli_profil->setSoyad($this->veli_soyad);
                    $veli_profil->setAd($this->veli_ad);
                    $veli_profil->setSoyad($this->veli_soyad);
                    $veli_profil->setKullanici($user);
                    $veli_profil->setMezuniyet($this->veli_mezuniyet);
                    $veli_profil->setMeslek($this->veli_meslek);
                    $veli_profil->setCinsiyet($this->veli_cinsiyeti);
                    $veli_profil->setYakinlik($this->veli_yakinlik);
                    $veli_profil->setVeli(1);
                    $veli_profil->setOkul($okul);

                    $em->persist($veli_profil);

                    /**
                     * Öğrenci nesnesini yakalaylım.
                     */
                    $ogrenci = $em -> getRepository( 'CoreCommonBundle:Ogrenci' ) -> findOneBy(array('tc'=>$this->veli_ogrenci_tc));


                    /**
                     * Veliye öğrenci ilişkisi sağlayalım
                     */
                    $veli_ogrenci = $em -> getRepository( 'CoreCommonBundle:OgrenciVeli' ) -> findOneBy(array('profil'=>$veli_profil,'ogrenci'=>$ogrenci));

                    /**
                     * Eğer yoksa..
                     */
                    if(!$veli_ogrenci && $ogrenci)
                    {
                        $veli_ogrenci = new OgrenciVeli();
                        $veli_ogrenci->setOgrenci($ogrenci);
                        $veli_ogrenci->setProfil($veli_profil);

                        $em->persist($veli_ogrenci);
                    }

                    $user->setRoles(array('ROLE_VELI'));

                    # Kullanıcı kaydını yapalım

                    #Kullanici ' ya okul ekleme
                    $kullanici = $em->getRepository('CoreCommonBundle:Kullanici')->find($user);

                    $kullanici->setOkul($okul);

                    $em->persist($kullanici);
                    #Kullanici ' ya okul ekleme
                }


                /**
                 * Verileri tekrardan sıfırlayalım.
                 */
                $this->veli_tc = "";
                $this->veli_ad = "";
                $this->veli_soyad = "";
                $this->veli_mail = "";
                $this->veli_cep = "";
                $this->veli_dogum_yeri = "";
                $this->veli_dogum_tarihi = "";
                $this->veli_cinsiyeti = "";
                $this->veli_yakinlik = "";
                $this->veli_mezuniyet = "";
                $this->veli_ogrenci_tc = "";
            }

            /**
             * Kaydı tamamlayalım.
             */
            $em->flush();

        }


        // Flash Bag Mesajı
        $this->get('session')->getFlashBag()->set(
            'info',
            'Veli listesi başarıyla içe aktarıldı!'
        );
    }

}