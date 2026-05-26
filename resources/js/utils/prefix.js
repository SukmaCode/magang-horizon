/**
 * URL prefix for subdirectory deployment.
 *
 * When deploying under a subdirectory (e.g. /internship), set this to the
 * subdirectory path. For root deployments, set to empty string ''.
 *
 * This value is read at build time by Vite, so after changing it you must
 * run `npm run build` again.
 */
const PREFIX = '/internship';

/**
 * Prepend the deployment prefix to a path.
 *
 * @param {string} path — must start with '/'
 * @returns {string}
 *
 * @example
 *   url('/login')          => '/internship/login'
 *   url('/admin/dashboard') => '/internship/admin/dashboard'
 */
export function url(path) {
    return `${PREFIX}${path}`;
}

export default PREFIX;
