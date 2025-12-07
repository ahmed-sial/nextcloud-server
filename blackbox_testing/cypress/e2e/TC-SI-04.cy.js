describe("TC-SI-04", () => {
  it("tests TC-SI-04", () => {
    cy.viewport(1089, 825);
      cy.visit("http://localhost:8080/login");
      cy.get("#password").clear();
    cy.get("#user").click();
    cy.get("#user").type("admin");
    cy.get("fieldset > button svg").click();
  });
});
