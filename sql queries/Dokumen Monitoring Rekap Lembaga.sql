SELECT
	rekap_lembaga.npsn
	, EXTRACT(YEAR FROM rekap_lembaga.tgl_simpan) as tahun
	, EXTRACT(MONTH FROM rekap_lembaga.tgl_simpan) as bulan
	, monitoring_lembaga.file_ppdb
FROM cutoff_rekap.rekap_lembaga
LEFT JOIN PUBLIC.monitoring_lembaga
	ON monitoring_lembaga.npsn = rekap_lembaga.npsn
	AND monitoring_lembaga.tahun = EXTRACT(YEAR FROM rekap_lembaga.tgl_simpan)
	AND monitoring_lembaga.bulan = EXTRACT(MONTH FROM rekap_lembaga.tgl_simpan)
WHERE rekap_lembaga.tgl_simpan = (
	SELECT MAX(rekap_lembaga.tgl_simpan)
	FROM cutoff_rekap.rekap_lembaga
	WHERE EXTRACT(YEAR FROM rekap_lembaga.tgl_simpan) = 2024
	AND EXTRACT(MONTH FROM rekap_lembaga.tgl_simpan) = 5
	)
	AND rekap_lembaga.npsn = '20560686'