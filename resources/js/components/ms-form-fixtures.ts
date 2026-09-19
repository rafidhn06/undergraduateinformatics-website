import { msw } from '@ladle/react';

import { type MsFormPayload } from '../types/ms-forms';

export const SUBMIT_URL = '/api/feedback-submissions';
export const SUBMIT_LABEL = 'Kirim';

export const richPayload: MsFormPayload = {
    link: 'https://forms.office.com/r/abc123',
    title: { text: 'Form Umpan Balik' },
    description: { text: 'Bantu kami meningkatkan layanan dengan mengisi formulir di bawah ini.' },
    sections: [
        {
            id: 'section-1',
            title: null,
            subtitle: { text: 'Semua pertanyaan bertanda * wajib diisi.' },
            questionIds: ['jenis', 'kategori', 'isi', 'tanggal'],
        },
    ],
    questions: [
        {
            id: 'jenis',
            title: { text: 'Jenis Masukan' },
            subtitle: null,
            type: 'choice',
            required: true,
            multiple: false,
            choices: [
                { value: 'Saran', label: { text: 'Saran' }, branchTargetId: null },
                { value: 'Keluhan', label: { text: 'Keluhan' }, branchTargetId: null },
                { value: 'Apresiasi', label: { text: 'Apresiasi' }, branchTargetId: null },
            ],
        },
        {
            id: 'kategori',
            title: { text: 'Aspek yang Dinilai' },
            subtitle: { text: 'Pilih semua yang relevan.' },
            type: 'choice',
            required: true,
            multiple: true,
            choices: [
                { value: 'Akademik', label: { text: 'Akademik' }, branchTargetId: null },
                { value: 'Administrasi', label: { text: 'Administrasi' }, branchTargetId: null },
                { value: 'Fasilitas', label: { text: 'Fasilitas' }, branchTargetId: null },
                { value: 'Lainnya', label: { text: 'Lainnya' }, branchTargetId: null },
            ],
        },
        {
            id: 'isi',
            title: { text: 'Isi Masukan' },
            subtitle: { text: 'Tulis masukan Anda secara singkat dan jelas.' },
            type: 'text',
            required: true,
            multiple: false,
            choices: [],
        },
        {
            id: 'tanggal',
            title: { text: 'Tanggal Pengalaman' },
            subtitle: { text: 'Kosongkan jika tidak ingin menyertakan tanggal.' },
            type: 'date',
            required: false,
            multiple: false,
            choices: [],
        },
    ],
};

export const richTextPayload: MsFormPayload = {
    link: 'https://forms.office.com/r/rt123',
    title: { text: 'Form Umpan Balik', html: 'Form <b>Umpan</b> Balik' },
    description: {
        text: 'Bantu kami meningkatkan layanan.',
        html: 'Bantu kami <b>meningkatkan</b> layanan.',
    },
    sections: [
        {
            id: 'section-1',
            title: { text: 'Jenis Masukan', html: 'Jenis <u>Masukan</u>' },
            subtitle: { text: 'Pilih satu atau lebih.', html: 'Pilih <i>satu</i> atau lebih.' },
            questionIds: ['kategori'],
        },
    ],
    questions: [
        {
            id: 'kategori',
            title: { text: 'Aspek yang Dinilai', html: 'Aspek yang <i>Dinilai</i>' },
            subtitle: { text: 'Tulis rincian Anda.', html: 'Tulis <b>rincian</b> Anda.' },
            type: 'choice',
            required: true,
            multiple: true,
            choices: [
                {
                    value: 'Akademik',
                    label: { text: 'Akademik', html: '<b>Akademik</b>' },
                    branchTargetId: null,
                },
                { value: 'Fasilitas', label: { text: 'Fasilitas' }, branchTargetId: null },
            ],
        },
    ],
};

export const branchingPayload: MsFormPayload = {
    link: 'https://forms.office.com/r/def456',
    title: { text: 'Form Umpan Balik' },
    description: { text: 'Pilih jenis masukan Anda untuk memulai.' },
    sections: [
        {
            id: 'section-pilih',
            title: { text: 'Jenis Masukan' },
            subtitle: { text: 'Jawaban Anda menentukan bagian yang akan diisi.' },
            questionIds: ['jenis'],
        },
        {
            id: 'section-saran',
            title: { text: 'Detail Saran' },
            subtitle: { text: 'Ceritakan saran Anda.' },
            questionIds: ['saran', 'dampak'],
        },
        {
            id: 'section-keluhan',
            title: { text: 'Detail Keluhan' },
            subtitle: { text: 'Berikan rincian agar kami dapat menindaklanjuti.' },
            questionIds: ['keluhan', 'tanggal'],
        },
    ],
    questions: [
        {
            id: 'jenis',
            title: { text: 'Jenis Masukan' },
            subtitle: null,
            type: 'choice',
            required: true,
            multiple: false,
            choices: [
                { value: 'Saran', label: { text: 'Saran' }, branchTargetId: 'saran' },
                { value: 'Keluhan', label: { text: 'Keluhan' }, branchTargetId: 'keluhan' },
                { value: 'Apresiasi', label: { text: 'Apresiasi' }, branchTargetId: 'end' },
            ],
        },
        {
            id: 'saran',
            title: { text: 'Tuliskan Saran Anda' },
            subtitle: { text: 'Semakin rinci semakin membantu kami.' },
            type: 'text',
            required: true,
            multiple: false,
            choices: [],
        },
        {
            id: 'dampak',
            title: { text: 'Seberapa Besar Dampaknya?' },
            subtitle: null,
            type: 'choice',
            required: true,
            multiple: false,
            choices: [
                { value: 'Besar', label: { text: 'Besar' }, branchTargetId: 'end' },
                { value: 'Sedang', label: { text: 'Sedang' }, branchTargetId: 'end' },
                { value: 'Kecil', label: { text: 'Kecil' }, branchTargetId: 'end' },
            ],
        },
        {
            id: 'keluhan',
            title: { text: 'Ceritakan Keluhan Anda' },
            subtitle: null,
            type: 'text',
            required: true,
            multiple: false,
            choices: [],
        },
        {
            id: 'tanggal',
            title: { text: 'Kapan Masalah Tersebut Terjadi?' },
            subtitle: null,
            type: 'date',
            required: true,
            multiple: false,
            choices: [],
        },
    ],
};

export const simplePayload: MsFormPayload = {
    link: 'https://forms.office.com/r/ghi789',
    title: { text: 'Form Umpan Balik' },
    description: null,
    sections: [
        {
            id: 'section-1',
            title: null,
            subtitle: { text: 'Semua pertanyaan wajib diisi.' },
            questionIds: ['isi'],
        },
    ],
    questions: [
        {
            id: 'isi',
            title: { text: 'Isi Masukan' },
            subtitle: null,
            type: 'text',
            required: true,
            multiple: false,
            choices: [],
        },
    ],
};

export const optionalPayload: MsFormPayload = {
    link: 'https://forms.office.com/r/opt999',
    title: { text: 'Form Umpan Balik' },
    description: null,
    sections: [
        {
            id: 'section-1',
            title: null,
            subtitle: null,
            questionIds: ['pesan'],
        },
    ],
    questions: [
        {
            id: 'pesan',
            title: { text: 'Pesan Anda' },
            subtitle: null,
            type: 'text',
            required: false,
            multiple: false,
            choices: [],
        },
    ],
};

export function toFormProps(payload: MsFormPayload) {
    return {
        title: payload.title,
        description: payload.description,
        sections: payload.sections,
        questions: payload.questions,
        submitUrl: SUBMIT_URL,
    };
}

export const submitOk = [msw.http.post(SUBMIT_URL, () => msw.HttpResponse.json({ success: true }))];

export const submitFail = [
    msw.http.post(SUBMIT_URL, () =>
        msw.HttpResponse.json(
            { message: 'Gagal mengirim formulir. Silakan coba beberapa saat lagi.' },
            { status: 422 }
        )
    ),
];
