import http from 'k6/http';
import { check, sleep } from 'k6';

const BASE_URL = 'http://127.0.0.1:8000';

export const options = {
  stages: [
    { duration: '20s', target: 10 },
    { duration: '40s', target: 20 },
    { duration: '20s', target: 0 },
  ],

  thresholds: {
    http_req_failed: ['rate<0.01'],
    http_req_duration: ['p(95)<800'],
  },
};

export default function () {
  const rutas = [
    '/',
    '/libros',
    '/prestamos',
    '/usuarios',
  ];

  for (const ruta of rutas) {
    const res = http.get(`${BASE_URL}${ruta}`);

    check(res, {
      [`${ruta} responde 200`]: (r) => r.status === 200,
    });

    sleep(1);
  }
}