import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  vus: 10,
  duration: '30s',

  thresholds: {
    http_req_failed: ['rate<0.01'], 
    http_req_duration: ['p(95)<500'],
  },
};

export default function () {
  const res = http.get('http://127.0.0.1:8000/libros');

  check(res, {
    'status es 200': (r) => r.status === 200,
    'respuesta menor a 500ms': (r) => r.timings.duration < 500,
    'contiene texto Libros': (r) => r.body.includes('Libros') || r.body.includes('libros'),
  });

  sleep(1);
}