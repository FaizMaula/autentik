def hitung_skor_akhir(match_score_kegiatan, match_score_nama, match_score_tanggal, S_search=20, S_gamma=10):
    S_final = (((match_score_kegiatan + match_score_nama + match_score_tanggal) / 3) * 0.5) + S_search + S_gamma
    if S_final >= 75:
        status = "TERPERCAYA"
    elif S_final >= 50:
        status = "PERLU DITINJAU"
    else:
        status = "TIDAK TERPERCAYA"
    return {"S_final": S_final, "status": status}
