export const parseError = (err: any) => {
  const res = err?.response?.data;
  if (res?.error) return res.error;
  if (res?.errors) {
    const firstKey = Object.keys(res.errors)[0];
    return res.errors[firstKey][0];
  }
  if (res?.message) return res.message;

  return "Something went wrong";
};