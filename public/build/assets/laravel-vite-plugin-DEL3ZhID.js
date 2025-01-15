async function f(n,e){for(const r of Array.isArray(n)?n:[n]){const o=e[r];if(!(typeof o>"u"))return typeof o=="function"?o():o}throw new Error(`Page not found: ${n}`)}export{f as r};
