<?php 
namespace App\Http\Libraries;

class IndikatorTahap2
{
	public static function cek_tahun($tahun){
		switch ($tahun) {
			case '2024':
				$indikator = IndikatorTahap2::indikator2024();
				break;
			case '2025':
				$indikator = IndikatorTahap2::indikator2025();
				break;
			
			default:
				$indikator;
				break;
		}

		return $indikator;
	}

	public static function indikator2024(){
		$data = [
			[
				'id'=>0,
				'kompetensi'=>'Kemampuan Bunda PAUD dalam memimpin, menginspirasi, dan memotivasi tim serta komunitas.',
				'indikator'=>[
					[
						'id'=>0,
						'text'=>'Memiliki visi dan misi yang jelas.',
					],
					[
						'id'=>1,
						'text'=>'Mampu menggerakkan tim kerja.',
					],
					[
						'id'=>2,
						'text'=>'Menunjukkan komitmen terhadap perkembangan PAUD.',
					]
				],
			],
			[
				'id'=>1,
				'kompetensi'=>'Tingkat inovasi dan kreativitas dalam merancang program dan kegiatan PAUD.',
				'indikator'=>[
					[
						'id'=>0,
						'text'=>'Mengembangkan program baru yang inovatif.',
					],
					[
						'id'=>1,
						'text'=>'Memanfaatkan sumber daya lokal secara kreatif.',
					],
					[
						'id'=>2,
						'text'=>'Menyusun kegiatan yang menarik dan edukatif.',
					]
				],
			],
			[
				'id'=>2,
				'kompetensi'=>'Kemampuan berkomunikasi dengan orang tua, anak, dan pemangku kepentingan lainnya serta menjalin kerja sama yang efektif.',
				'indikator'=>[
					[
						'id'=>0,
						'text'=>'Mampu berkomunikasi dengan jelas dan efektif.',
					],
					[
						'id'=>1,
						'text'=>'Menjalin kerja sama dengan berbagai pihak.',
					],
					[
						'id'=>2,
						'text'=>'Meningkatkan partisipasi orang tua dalam kegiatan PAUD.',
					]
				],
			],
			[
				'id'=>3,
				'kompetensi'=>'Tingkat dedikasi dan komitmen terhadap perkembangan dan pendidikan anak usia dini.',
				'indikator'=>[
					[
						'id'=>0,
						'text'=>'Aktif dalam berbagai kegiatan pendidikan anak usia dini.',
					],
					[
						'id'=>1,
						'text'=>'Menunjukkan perhatian terhadap perkembangan anak.',
					],
					[
						'id'=>2,
						'text'=>'Terlibat dalam upaya peningkatan kualitas PAUD',
					]
				],
			],
			[
				'id'=>4,
				'kompetensi'=>'Kemampuan dalam mengelola administrasi PAUD secara tertib dan rapi.',
				'indikator'=>[
					[
						'id'=>0,
						'text'=>'Administrasi PAUD yang tertib dan up-to-date.',
					],
					[
						'id'=>1,
						'text'=>'Pengelolaan data anak dan kegiatan dengan baik.',
					],
					[
						'id'=>2,
						'text'=>'Laporan kegiatan yang lengkap dan tepat waktu',
					]
				],
			],
		];

		return $data;
	}

	public static function indikator2025(){
		$data = [
			[
				'id'=>0,
				'kompetensi'=>'Kemampuan Bunda PAUD dalam memimpin, menginspirasi, dan memotivasi tim serta komunitas.',
				'indikator'=>[
					[
						'id'=>0,
						'text'=>'Memiliki visi dan misi yang jelas untuk PAUD di wilayahnya.',
					],
					[
						'id'=>1,
						'text'=>'Mampu menggerakkan dan memotivasi tim kerja Pokja Bunda PAUD.',
					],
					[
						'id'=>2,
						'text'=>'Menjadi figur dan tokoh sentral dalam mendukung kebijakan PAUD Bermutu.',
					],
					[
						'id'=>3,
						'text'=>'Melakukan pembinaan dan pengawasan implementasi kebijakan PAUD Bermutu.',
					]
				],
			],
			[
				'id'=>1,
				'kompetensi'=>'Tingkat inovasi dan kreativitas dalam merancang program dan kegiatan PAUD.',
				'indikator'=>[
					[
						'id'=>0,
						'text'=>'Mengembangkan Narasi Publik Program yang efektif untuk sosialisasi PAUD Bermutu.',
					],
					[
						'id'=>1,
						'text'=>'Mengembangkan program baru yang inovatif dan menarik.',
					],
					[
						'id'=>2,
						'text'=>'Memanfaatkan teknologi digital dan media sosial untuk sosialisasi.',
					],
					[
						'id'=>3,
						'text'=>'Melakukan evaluasi program kerja secara berkala.',
					]
				],
			],
			[
				'id'=>2,
				'kompetensi'=>'Kemampuan berkomunikasi dengan orang tua, anak, dan pemangku kepentingan lainnya serta menjalin kerja sama yang efektif.',
				'indikator'=>[
					[
						'id'=>0,
						'text'=>'Mampu berkomunikasi dengan jelas dan efektif kepada berbagai pihak.',
					],
					[
						'id'=>1,
						'text'=>'Menjalin kerja sama dengan berbagai pihak lintas sektor.',
					],
					[
						'id'=>2,
						'text'=>'Meningkatkan partisipasi orang tua dalam kegiatan PAUD.',
					],
					[
						'id'=>3,
						'text'=>'Mendorong pembentukan/pengaktifan Forum Komunikasi PAUD-SD.',
					]
				],
			],
			[
				'id'=>3,
				'kompetensi'=>'Tingkat dedikasi dan komitmen terhadap perkembangan dan pendidikan anak usia dini.',
				'indikator'=>[
					[
						'id'=>0,
						'text'=>'Terlibat aktif dalam upaya peningkatan kualitas PAUD.',
					],
					[
						'id'=>1,
						'text'=>'Mendorong peningkatan kualifikasi dan kompetensi PTK PAUD.',
					],
					[
						'id'=>2,
						'text'=>'Melaksanakan monitoring dan motivasi kepada satuan PAUD.',
					]
				],
			],
			[
				'id'=>4,
				'kompetensi'=>'Kemampuan dalam mengelola administrasi PAUD secara tertib dan rapi.',
				'indikator'=>[
					[
						'id'=>0,
						'text'=>'Administrasi PAUD yang tertib dan up-to-date.',
					],
					[
						'id'=>1,
						'text'=>'Pengelolaan data anak dan kegiatan dengan baik.',
					],
					[
						'id'=>2,
						'text'=>'Laporan kegiatan yang lengkap dan tepat waktu',
					]
				],
			],
		];

		return $data;
	}
}
?>