// utils/fetchWithCancel.js
class CancelableFetch {
  constructor() {
    this.controller = null;
    this.timeoutId = null;
  }

  /**
   * Cancel the current fetch request and clear timeout
   */
  cancel() {
    if (this.controller) {
      this.controller.abort();
      this.controller = null;
    }
    if (this.timeoutId) {
      clearTimeout(this.timeoutId);
      this.timeoutId = null;
    }
  }

  /**
   * Fetch with debounce and cancellation support
   * @param {string} url - API endpoint
   * @param {object} options - Fetch options
   * @param {number} debounceMs - Debounce delay in milliseconds
   * @returns {Promise} Fetch promise or null if cancelled
   */
  async debounceAndFetch(url, options = {}, debounceMs = 300) {
    // Cancel any pending request
    this.cancel();

    // Return a promise that resolves after debounce
    return new Promise((resolve, reject) => {
      this.timeoutId = setTimeout(async () => {
        try {
          // Create new AbortController for this request
          this.controller = new AbortController();

          const response = await fetch(url, {
            ...options,
            signal: this.controller.signal,
          });

          if (!response.ok) {
            throw new Error(`HTTP ${response.status}: Failed to fetch`);
          }

          const data = await response.json();
          this.controller = null;
          resolve(data);
        } catch (error) {
          // Don't reject if the request was aborted (cancelled)
          if (error.name !== 'AbortError') {
            reject(error);
          } else {
            resolve(null); // Return null for cancelled requests
          }
        }
      }, debounceMs);
    });
  }

  /**
   * Immediate fetch with cancellation support (no debounce)
   * @param {string} url - API endpoint
   * @param {object} options - Fetch options
   * @returns {Promise} Fetch promise
   */
  async fetchWithCancel(url, options = {}) {
    // Cancel any pending request
    this.cancel();

    try {
      this.controller = new AbortController();

      const response = await fetch(url, {
        ...options,
        signal: this.controller.signal,
      });

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: Failed to fetch`);
      }

      const data = await response.json();
      this.controller = null;
      return data;
    } catch (error) {
      if (error.name !== 'AbortError') {
        throw error;
      }
      return null; // Return null for cancelled requests
    }
  }
}

// Create singleton instance
export const cancelableFetch = new CancelableFetch();

export default CancelableFetch;